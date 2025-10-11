// ===============================
// Helper
// ===============================
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

// ===============================
// Font & Background
// ===============================
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
  `;
  document.head.appendChild(styleEl);
  root.classList.add("exporting");

  // strip border/outline/pill seperti sebelumnya …
  const toClean = [];
  root.querySelectorAll("*").forEach((el) => {
    const cs = window.getComputedStyle(el);
    const hasDashed = ["borderTopStyle","borderRightStyle","borderBottomStyle","borderLeftStyle"]
      .some(k => cs[k] === "dashed");
    if (hasDashed || el.classList.contains("inline-edit") || el.classList.contains("pill")) {
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
      el.style.border = el.style.borderTop = el.style.borderRight =
      el.style.borderBottom = el.style.borderLeft = "none";
      el.style.outline = el.style.boxShadow = "none";
    }
  });

  // pastikan elemen yang wajib hilang benar2 disembunyikan (backup display)
  const hiddenEls = Array.from(
    root.querySelectorAll(".export-hidden, .drag-indicator, .delete-indicator")
  );
  hiddenEls.forEach(el => { el.dataset._display = el.style.display; el.style.display = "none"; });

  return () => {
    toClean.forEach(s => {
      s.el.style.border = s.border;
      s.el.style.borderTop = s.borderTop;
      s.el.style.borderRight = s.borderRight;
      s.el.style.borderBottom = s.borderBottom;
      s.el.style.borderLeft = s.borderLeft;
      s.el.style.outline = s.outline;
      s.el.style.boxShadow = s.boxShadow;
    });
    hiddenEls.forEach(el => { el.style.display = el.dataset._display || ""; delete el.dataset._display; });
    root.classList.remove("exporting");
    styleEl.remove();
  };
}

// ===============================
// Download CV (Image / PDF)
// ===============================
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

async function downloadAsPDF() {
    const content = document.getElementById("content");
    if (!content) return;

    const cleanup = applyExportMask(content);
    const canvas = await html2canvas(content, {
        scale: 2,
        useCORS: true,
        allowTaint: true,
    });
    cleanup();

    const imgWidthPx = canvas.width;
    const imgHeightPx = canvas.height;

    const pdf = new jspdf.jsPDF("p", "mm", "a4");
    const pageWidthMm = pdf.internal.pageSize.getWidth();
    const pageHeightMm = pdf.internal.pageSize.getHeight();

    const marginTop = 10,
        marginBottom = 15,
        marginLeft = 8,
        marginRight = 8;
    const availableWidthMm = pageWidthMm - marginLeft - marginRight;
    const availableHeightMm = pageHeightMm - marginTop - marginBottom;

    const imgProps = pdf.getImageProperties(canvas);
    const pdfHeight = (imgProps.height * availableWidthMm) / imgProps.width;

    if (pdfHeight <= availableHeightMm) {
        const imgData = canvas.toDataURL("image/png");
        pdf.addImage(
            imgData,
            "PNG",
            marginLeft,
            marginTop,
            availableWidthMm,
            pdfHeight
        );
    } else {
        const pxPerMm = imgWidthPx / pageWidthMm;
        const sliceHeightPx = Math.floor(availableHeightMm * pxPerMm);
        let positionY = 0,
            pageIndex = 0;

        while (positionY < imgHeightPx) {
            const currentSliceHeightPx = Math.min(
                sliceHeightPx,
                imgHeightPx - positionY
            );
            const sliceCanvas = document.createElement("canvas");
            sliceCanvas.width = imgWidthPx;
            sliceCanvas.height = currentSliceHeightPx;
            const ctx = sliceCanvas.getContext("2d");

            ctx.drawImage(
                canvas,
                0,
                positionY,
                imgWidthPx,
                currentSliceHeightPx,
                0,
                0,
                imgWidthPx,
                currentSliceHeightPx
            );

            const imgData = sliceCanvas.toDataURL("image/png");
            const sliceHeightMm = currentSliceHeightPx / pxPerMm;

            if (pageIndex > 0) pdf.addPage();
            pdf.addImage(
                imgData,
                "PNG",
                marginLeft,
                marginTop,
                availableWidthMm,
                sliceHeightMm
            );

            positionY += currentSliceHeightPx;
            pageIndex++;
        }
    }

    pdf.save("cv.pdf");
}

// ===============================
// Email & Print
// ===============================
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

// ===============================
// Save to Dashboard (PDF upload)
// ===============================
async function savePdfToDashboard() {
    const btn = document.getElementById("saveToDashboardBtn");
    if (!btn) return;

    btn.disabled = true;
    btn.innerText = "Saving...";

    try {
        const content = document.getElementById("content");

        const cleanup = applyExportMask(content);
        const canvas = await html2canvas(content, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
        });
        cleanup();

        const imgData = canvas.toDataURL("image/png");

        const pdf = new jspdf.jsPDF("p", "mm", "a4");
        const pageWidth = pdf.internal.pageSize.getWidth();
        const imgProps = pdf.getImageProperties(imgData);
        const pdfHeight = (imgProps.height * pageWidth) / imgProps.width;
        pdf.addImage(imgData, "PNG", 0, 0, pageWidth, pdfHeight);

        const pdfBlob = pdf.output("blob");

        const formData = new FormData();
        formData.append("cv_file", pdfBlob, "cv.pdf");

        const templateId = btn.dataset.templateId;
        if (!templateId) {
            alert("Template ID tidak ditemukan!");
            btn.disabled = false;
            btn.innerText = "Save to Dashboard";
            return;
        }
        formData.append("template_id", templateId);

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        formData.append("_token", csrfToken);

        const res = await fetch("/curriculum-vitae/save", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            body: formData,
        });

        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            console.error("Server returned non-JSON response:", text);
            alert("Server error — Response bukan JSON.");
            return;
        }

        if (data.success) {
            if (window.Swal)
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: data.message,
                    timer: 1800,
                    showConfirmButton: false,
                });
            else alert("CV berhasil disimpan");
        } else {
            const msg =
                data.message || JSON.stringify(data.errors) || "Unknown error";
            if (window.Swal)
                Swal.fire({ icon: "error", title: "Gagal", text: msg });
            else alert("Gagal menyimpan CV: " + msg);
        }
    } catch (err) {
        console.error("JS exception", err);
        alert("Terjadi error saat proses penyimpanan (lihat console).");
    } finally {
        btn.disabled = false;
        btn.innerText = "Save to Dashboard";
    }
}

// ===============================
// Event Binding
// ===============================
document.addEventListener("DOMContentLoaded", () => {
    const btnSave = document.getElementById("saveToDashboardBtn");
    if (btnSave) btnSave.addEventListener("click", savePdfToDashboard);
});
