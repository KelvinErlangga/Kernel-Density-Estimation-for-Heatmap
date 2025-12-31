function hexToRgb(hex) {
    hex = hex.replace(/^#/, "");
    if (hex.length === 3)
        hex = hex
            .split("")
            .map((x) => x + x)
            .join("");
    const num = parseInt(hex, 16);
    return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
}

// Font & Background
function changeFont(selectElement) {
    const content = document.getElementById("content");
    if (content) content.style.fontFamily = selectElement.value;
}

function changeBackgroundColor(color) {
    let panel =
        document.querySelector(".left-panel") ||
        document.getElementById("content");
    if (!panel) return;

    panel.style.backgroundColor = color;

    const rgb = hexToRgb(color);
    const brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
    const textColor = brightness > 128 ? "#000000" : "#FFFFFF";

    panel.style.color = textColor;
    panel.querySelectorAll("p,h1,h2,h3,a,span,div,li").forEach((el) => {
        el.style.color = textColor;
    });
}

// ===== Export mask: hilangkan outline/border saat snapshot =====
function applyExportMask(root) {
    if (!root) return () => {};

    const styleEl = document.createElement("style");
    styleEl.id = "export-mask-style";
    styleEl.textContent = `
        #content.exporting .inline-edit:empty:before { content: "" !important; }
        #content.exporting .export-hidden,
        #content.exporting .drag-indicator,
        #content.exporting .delete-indicator { display: none !important; }

        #content.exporting .cv-link,
        #content.exporting .cv-link:visited,
        #content.exporting .cv-link:hover,
        #content.exporting .cv-link:active{
            color:#2563eb !important;
            text-decoration:none !important;
            position:relative !important;
            display:inline-block !important;
            padding-bottom:2px !important;
        }

        #content.exporting .cv-link::after{
            content:"" !important;
            position:absolute !important;
            left:0 !important;
            right:0 !important;
            bottom:0 !important;
            height:1px !important;
            background:#2563eb !important;
            border-radius:0 !important;
        }

        #content.exporting .cv-link *{
            color:inherit !important;
            text-decoration:none !important;
        }
    `;

    document.head.appendChild(styleEl);
    root.classList.add("exporting");

    const toClean = [];
    root.querySelectorAll("*").forEach((el) => {
        const cs = window.getComputedStyle(el);
        const hasDashed = [
            "borderTopStyle",
            "borderRightStyle",
            "borderBottomStyle",
            "borderLeftStyle",
        ].some((k) => cs[k] === "dashed");
        if (
            hasDashed ||
            el.classList.contains("inline-edit") ||
            el.classList.contains("pill")
        ) {
            toClean.push({
                el,
                border: el.style.border,
                borderTop: el.style.borderTop,
                borderRight: el.style.borderRight,
                borderBottom: el.style.borderBottom,
                borderLeft: el.style.borderLeft,
                outline: el.style.outline,
                boxShadow: el.style.boxShadow,
            });
            el.style.border =
                el.style.borderTop =
                el.style.borderRight =
                el.style.borderBottom =
                el.style.borderLeft =
                    "none";
            el.style.outline = el.style.boxShadow = "none";
        }
    });

    const hiddenEls = Array.from(
        root.querySelectorAll(
            ".export-hidden, .drag-indicator, .delete-indicator"
        )
    );
    hiddenEls.forEach((el) => {
        el.dataset._display = el.style.display;
        el.style.display = "none";
    });

    return () => {
        toClean.forEach((s) => {
            s.el.style.border = s.border;
            s.el.style.borderTop = s.borderTop;
            s.el.style.borderRight = s.borderRight;
            s.el.style.borderBottom = s.borderBottom;
            s.el.style.borderLeft = s.borderLeft;
            s.el.style.outline = s.outline;
            s.el.style.boxShadow = s.boxShadow;
        });
        hiddenEls.forEach((el) => {
            el.style.display = el.dataset._display || "";
            delete el.dataset._display;
        });
        root.classList.remove("exporting");
        styleEl.remove();
    };
}

// Download CV (Image / PDF)
async function downloadAsImage(type) {
    const content = document.getElementById("content");
    if (!content) return;

    const cleanup = applyExportMask(content);
    const canvas = await html2canvas(content, {
        scale: 2,
        useCORS: true,
        allowTaint: true,
    });
    cleanup();

    const image = canvas.toDataURL(`image/${type}`);
    const link = document.createElement("a");
    link.href = image;
    link.download = `cv.${type}`;
    link.click();
}

function findSafeCutY(
    canvas,
    startY,
    targetHeight,
    scanBackPx = 360,
    opts = {}
) {
    const {
        safetyPadPx = 26,
        consecutiveRows = 10,
        minBlankRatio = 0.985,
        colorTol = 18,
    } = opts;

    const w = canvas.width;
    const h = canvas.height;
    const idealEndY = Math.min(startY + targetHeight, h);
    if (idealEndY <= startY) return idealEndY;

    const ctx = canvas.getContext("2d", { willReadFrequently: true });

    // ambil warna bg referensi
    const bg = ctx.getImageData(4, Math.min(startY + 4, h - 1), 1, 1).data;
    const bgR = bg[0],
        bgG = bg[1],
        bgB = bg[2];

    const scanTop = Math.max(
        startY + Math.floor(targetHeight * 0.72),
        idealEndY - scanBackPx
    );
    if (scanTop >= idealEndY) return idealEndY;

    const scanHeight = idealEndY - scanTop;
    const img = ctx.getImageData(0, scanTop, w, scanHeight).data;

    // sampling lebih rapat supaya tidak miss teks
    const xStep = Math.max(6, Math.floor(w / 520));

    function isBgPixel(r, g, b, a) {
        if (a < 10) return true;
        return (
            Math.abs(r - bgR) <= colorTol &&
            Math.abs(g - bgG) <= colorTol &&
            Math.abs(b - bgB) <= colorTol
        );
    }

    // cek baris kosong PER BAGIAN: kiri & kanan (hindari false-blank karena tengah putih)
    function blankRatioForRange(rowOffset, xStart, xEnd) {
        let blank = 0,
            total = 0;
        for (let x = xStart; x < xEnd; x += xStep) {
            const idx = rowOffset + x * 4;
            const r = img[idx],
                g = img[idx + 1],
                b = img[idx + 2],
                a = img[idx + 3];
            total++;
            if (isBgPixel(r, g, b, a)) blank++;
        }
        return blank / total;
    }

    const leftStart = Math.floor(w * 0.06);
    const leftEnd = Math.floor(w * 0.47);
    const rightStart = Math.floor(w * 0.53);
    const rightEnd = Math.floor(w * 0.94);

    let run = 0;

    for (let localY = scanHeight - 1; localY >= 0; localY--) {
        const rowOffset = localY * w * 4;

        const leftBlank = blankRatioForRange(rowOffset, leftStart, leftEnd);
        const rightBlank = blankRatioForRange(rowOffset, rightStart, rightEnd);

        // baris dianggap kosong hanya jika KIRI & KANAN sama-sama kosong
        if (leftBlank >= minBlankRatio && rightBlank >= minBlankRatio) {
            run++;
            if (run >= consecutiveRows) {
                const cut = scanTop + localY - safetyPadPx;
                return Math.max(startY + 1, Math.min(cut, idealEndY));
            }
        } else {
            run = 0;
        }
    }

    return idealEndY;
}

function buildPdfFromCanvasPaged(canvas, opts = {}) {
    const {
        marginFirstTop = 10,
        marginFirstBottom = 15,
        marginOtherTop = 28,
        marginOtherBottom = 28,
        marginLeft = 8,
        marginRight = 8,
        overlapPx = 70, // BESARKAN overlap (lebih aman dari kepotong)
        imageQuality = 0.9,
    } = opts;

    const pdf = new jspdf.jsPDF("p", "mm", "a4");
    const pageWidthMm = pdf.internal.pageSize.getWidth();
    const pageHeightMm = pdf.internal.pageSize.getHeight();
    const availableWidthMm = pageWidthMm - marginLeft - marginRight;

    const imgWidthPx = canvas.width;
    const imgHeightPx = canvas.height;
    const pxPerMm = imgWidthPx / availableWidthMm;

    const getAvailHeightPx = (pageIndex) => {
        const mt = pageIndex === 0 ? marginFirstTop : marginOtherTop;
        const mb = pageIndex === 0 ? marginFirstBottom : marginOtherBottom;
        return Math.floor((pageHeightMm - mt - mb) * pxPerMm);
    };

    let y = 0;
    let pageIndex = 0;
    const maxPagesGuard = 12;

    while (y < imgHeightPx && pageIndex < maxPagesGuard) {
        const mt = pageIndex === 0 ? marginFirstTop : marginOtherTop;
        const targetH = getAvailHeightPx(pageIndex);
        const remaining = imgHeightPx - y;

        // last page
        if (remaining <= targetH + 2) {
            const sliceCanvas = document.createElement("canvas");
            sliceCanvas.width = imgWidthPx;
            sliceCanvas.height = remaining;
            sliceCanvas
                .getContext("2d")
                .drawImage(
                    canvas,
                    0,
                    y,
                    imgWidthPx,
                    remaining,
                    0,
                    0,
                    imgWidthPx,
                    remaining
                );

            const imgData = sliceCanvas.toDataURL("image/jpeg", imageQuality);
            const sliceHeightMm = remaining / pxPerMm;

            if (pageIndex > 0) pdf.addPage();
            pdf.addImage(
                imgData,
                "JPEG",
                marginLeft,
                mt,
                availableWidthMm,
                sliceHeightMm,
                undefined,
                "FAST"
            );
            break;
        }

        // cari safe cut + padding
        const safeCutY = findSafeCutY(canvas, y, targetH, 360, {
            safetyPadPx: 26,
            consecutiveRows: 10,
            minBlankRatio: 0.985,
            colorTol: 18,
        });

        let sliceH = safeCutY - y;

        // jangan sampai slice terlalu pendek
        const minSlice = Math.floor(targetH * 0.88); // dinaikkan dari 0.82 -> 0.88
        if (sliceH < minSlice) sliceH = targetH;

        sliceH = Math.min(sliceH, remaining);

        const sliceCanvas = document.createElement("canvas");
        sliceCanvas.width = imgWidthPx;
        sliceCanvas.height = sliceH;
        sliceCanvas
            .getContext("2d")
            .drawImage(
                canvas,
                0,
                y,
                imgWidthPx,
                sliceH,
                0,
                0,
                imgWidthPx,
                sliceH
            );

        const imgData = sliceCanvas.toDataURL("image/jpeg", imageQuality);
        const sliceHeightMm = sliceH / pxPerMm;

        if (pageIndex > 0) pdf.addPage();
        pdf.addImage(
            imgData,
            "JPEG",
            marginLeft,
            mt,
            availableWidthMm,
            sliceHeightMm,
            undefined,
            "FAST"
        );

        // advance aman
        const advance = Math.max(minSlice, sliceH - overlapPx);
        y += advance;
        pageIndex++;
    }

    return pdf;
}

// async function downloadAsPDF() {
//     const content = document.getElementById("content");
//     if (!content) return;

//     const cleanup = applyExportMask(content);

//     const canvas = await html2canvas(content, {
//         scale: 2,
//         useCORS: true,
//         allowTaint: true,
//         backgroundColor: "#ffffff",
//         scrollY: -window.scrollY,
//         windowWidth: content.scrollWidth,
//         windowHeight: content.scrollHeight,
//     });

//     cleanup();

//     const pdf = buildPdfFromCanvasPaged(canvas, {
//         marginFirstTop: 10,
//         marginFirstBottom: 15,
//         marginOtherTop: 28,
//         marginOtherBottom: 28,
//         marginLeft: 8,
//         marginRight: 8,
//         overlapPx: 120, // <== samakan
//         imageQuality: 0.9,
//     });

//     pdf.save("CVRE - Generate.pdf");
// }


function downloadPdfText() {
    // aktifkan mode export biar editor & tombol hilang
    document.body.classList.add("export-mode");

    // kasih jeda sedikit biar CSS kebaca dulu
    setTimeout(() => {
        window.print();
    }, 50);
}

// balikin UI setelah print selesai
window.addEventListener("afterprint", () => {
    document.body.classList.remove("export-mode");
});

// Email & Print
function sendByEmail() {
    const emailBody = "Silakan temukan CV terlampir di bawah ini.";
    const mailtoLink = `mailto:?subject=CV&body=${encodeURIComponent(
        emailBody
    )}`;
    window.location.href = mailtoLink;
}
function printCV() {
    window.print();
}

// Spinner Button Helpers
function ensureSpinnerCss() {
    if (document.getElementById("btn-spinner-style")) return;
    const css = document.createElement("style");
    css.id = "btn-spinner-style";
    css.textContent = `
    @keyframes spin360 { to { transform: rotate(360deg); } }
    .btn-loading { pointer-events: none; opacity: .9; }
    .btn-loading .spinner {
      width: 16px; height: 16px; margin-right: 8px;
      display: inline-block; vertical-align: -2px;
    }
    .btn-loading .spinner svg { width: 16px; height: 16px; animation: spin360 1s linear infinite; }
  `;
    document.head.appendChild(css);
}

function setBtnLoading(btn, isLoading, textWhenLoading = "Saving...") {
    if (!btn) return;
    ensureSpinnerCss();

    if (isLoading) {
        if (!btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;
        btn.classList.add("btn-loading");
        btn.disabled = true;

        const spinnerSvg = `
      <span class="spinner" aria-hidden="true">
        <svg viewBox="0 0 50 50" fill="none">
          <circle cx="25" cy="25" r="20" stroke="currentColor" stroke-opacity=".2" stroke-width="6"/>
          <path d="M45 25a20 20 0 0 1-20 20" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
        </svg>
      </span>`;
        btn.innerHTML = `${spinnerSvg}<span>${textWhenLoading}</span>`;
    } else {
        btn.classList.remove("btn-loading");
        btn.disabled = false;
        if (btn.dataset.originalHtml) {
            btn.innerHTML = btn.dataset.originalHtml;
            delete btn.dataset.originalHtml;
        } else {
            btn.textContent = "Simpan ke Dashboard";
        }
    }
}

// Save to Dashboard (PDF upload)
// async function savePdfToDashboard() {
//     const btn = document.getElementById("saveToDashboardBtn");
//     if (!btn) return;

//     setBtnLoading(btn, true, "Saving...");

//     try {
//         const content = document.getElementById("content");
//         if (!content) return;

//         const cleanup = applyExportMask(content);
//         const canvas = await html2canvas(content, {
//             scale: 1.5,
//             useCORS: true,
//             allowTaint: true,
//         });
//         cleanup();

//         // SAMAKAN DENGAN DOWNLOAD: page 1 normal, page 2 dst lega
//         const pdf = buildPdfFromCanvasPaged(canvas, {
//             marginFirstTop: 10,
//             marginFirstBottom: 15,
//             marginOtherTop: 28,
//             marginOtherBottom: 28,
//             marginLeft: 8,
//             marginRight: 8,
//             overlapPx: 40,
//             imageQuality: 0.85,
//         });

//         const pdfBlob = pdf.output("blob");

//         const formData = new FormData();
//         formData.append("cv_file", pdfBlob, "cv.pdf");

//         const templateId = btn.dataset.templateId;
//         if (!templateId) {
//             alert("Template ID tidak ditemukan!");
//             return;
//         }
//         formData.append("template_id", templateId);

//         const csrfToken =
//             document
//                 .querySelector('meta[name="csrf-token"]')
//                 ?.getAttribute("content") || "";
//         formData.append("_token", csrfToken);

//         const res = await fetch("/curriculum-vitae/save", {
//             method: "POST",
//             headers: { "X-CSRF-TOKEN": csrfToken },
//             body: formData,
//         });

//         const text = await res.text();
//         let data;
//         try {
//             data = JSON.parse(text);
//         } catch (err) {
//             console.error("Server returned non-JSON response:", text);
//             alert("Server error — Response bukan JSON.");
//             return;
//         }

//         if (data.success) {
//             const newId = (data.data && data.data.id) || data.new_cv_id;
//             if (newId) {
//                 localStorage.setItem("cv_just_saved_id", String(newId));
//                 localStorage.setItem("cv_just_saved_at", String(Date.now()));
//             }
//             if (window.Swal) {
//                 await Swal.fire({
//                     icon: "success",
//                     title: "Berhasil",
//                     text: data.message || "CV berhasil disimpan",
//                     timer: 1500,
//                     showConfirmButton: false,
//                 });
//             } else {
//                 alert("CV berhasil disimpan");
//             }
//         } else {
//             const msg =
//                 data.message || JSON.stringify(data.errors) || "Unknown error";
//             if (window.Swal)
//                 Swal.fire({ icon: "error", title: "Gagal", text: msg });
//             else alert("Gagal menyimpan CV: " + msg);
//         }
//     } catch (err) {
//         console.error("JS exception", err);
//         alert("Terjadi error saat proses penyimpanan (lihat console).");
//     } finally {
//         setBtnLoading(document.getElementById("saveToDashboardBtn"), false);
//     }
// }

async function savePdfToDashboard() {
    const btn = document.getElementById("saveToDashboardBtn");
    if (!btn) return;

    const baseUrl = btn.dataset.saveTextPdfUrl;
    if (!baseUrl) {
        console.error("saveTextPdfUrl tidak ditemukan. Pastikan tombol punya data-save-text-pdf-url");
        alert("Link save PDF belum terpasang.");
        return;
    }

    setBtnLoading(btn, true, "Saving...");

    try {
        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

        const lang = localStorage.getItem("cv_lang") || "id";

        const res = await fetch(baseUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({ lang })
        });

        const data = await res.json();

        if (!data.success) throw data;

        if (window.Swal) {
            await Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: data.message || "PDF berhasil disimpan",
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            alert(data.message || "PDF berhasil disimpan");
        }

    } catch (err) {
        console.error(err);
        const msg = err?.message || "Gagal menyimpan PDF (cek console)";
        if (window.Swal) Swal.fire({ icon: "error", title: "Gagal", text: msg });
        else alert(msg);
    } finally {
        setBtnLoading(btn, false);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const btnSave = document.getElementById("saveToDashboardBtn");
    if (btnSave) btnSave.addEventListener("click", savePdfToDashboard);
});
