function hexToRgb(hex) {
  hex = hex.replace(/^#/, "");
  if (hex.length === 3) hex = hex.split("").map((x) => x + x).join("");
  const num = parseInt(hex, 16);
  return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
}

// Font & Background
function changeFont(selectElement) {
  const content = document.getElementById("content");
  if (content) content.style.fontFamily = selectElement.value;
}

function changeBackgroundColor(color) {
  let panel = document.querySelector(".left-panel") || document.getElementById("content");
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

// Download CV (Image / PDF)
async function downloadAsImage(type) {
  const content = document.getElementById("content");
  if (!content) return;

  const cleanup = applyExportMask(content);
  const canvas = await html2canvas(content, { scale: 2, useCORS: true, allowTaint: true });
  cleanup();

  const image = canvas.toDataURL(`image/${type}`);
  const link = document.createElement("a");
  link.href = image;
  link.download = `cv.${type}`;
  link.click();
}

/**
 * Cari garis "aman" (baris putih/kosong) dekat batas bawah slice,
 * supaya page break tidak memotong teks.
 *
 * - startY: awal slice (px)
 * - targetHeight: tinggi slice ideal (px)
 * - scanBackPx: mundur dari batas bawah untuk cari whitespace
 */
function findSafeCutY(canvas, startY, targetHeight, scanBackPx = 180) {
  const w = canvas.width;
  const h = canvas.height;
  const endY = Math.min(startY + targetHeight, h);
  const scanTop = Math.max(startY + Math.floor(targetHeight * 0.65), endY - scanBackPx);
  if (scanTop >= endY) return endY;

  const ctx = canvas.getContext("2d", { willReadFrequently: true });
  const scanHeight = endY - scanTop;

  // Ambil blok imageData sekali (lebih cepat)
  const img = ctx.getImageData(0, scanTop, w, scanHeight).data;

  // sampling supaya ringan
  const xStep = Math.max(8, Math.floor(w / 250)); // kira2 250 sample per baris
  const whiteThreshold = 245; // pixel dianggap "putih" kalau RGB > 245
  const minWhiteRatio = 0.985; // harus hampir full putih biar dianggap whitespace

  // scan dari bawah ke atas cari baris yg paling putih
  for (let localY = scanHeight - 1; localY >= 0; localY--) {
    let whiteCount = 0;
    let total = 0;

    const rowOffset = localY * w * 4;
    for (let x = 0; x < w; x += xStep) {
      const idx = rowOffset + x * 4;
      const r = img[idx];
      const g = img[idx + 1];
      const b = img[idx + 2];
      const a = img[idx + 3];

      total++;
      if (a > 0 && r >= whiteThreshold && g >= whiteThreshold && b >= whiteThreshold) {
        whiteCount++;
      }
    }

    const ratio = whiteCount / total;
    if (ratio >= minWhiteRatio) {
      return scanTop + localY;
    }
  }

  return endY; // fallback: potong di batas ideal
}

/**
 * Build PDF dari canvas dengan:
 * - margin page 1 normal (seperti kode awal)
 * - margin page 2 dst lebih besar
 * - pemotongan lebih aman (safe cut + overlap)
 */
function buildPdfFromCanvasPaged(canvas, opts = {}) {
  const {
    // margin page 1 (KEMBALIKAN KE AWAL)
    marginFirstTop = 10,
    marginFirstBottom = 15,

    // margin page 2 dst (DIBESARKAN)
    marginOtherTop = 25,
    marginOtherBottom = 25,

    // kiri kanan bisa tetap
    marginLeft = 8,
    marginRight = 8,

    // overlap antar halaman supaya teks yang kepotong tidak hilang
    overlapPx = 36,

    // kualitas gambar
    imageQuality = 0.85,
  } = opts;

  const pdf = new jspdf.jsPDF("p", "mm", "a4");

  const pageWidthMm = pdf.internal.pageSize.getWidth();
  const pageHeightMm = pdf.internal.pageSize.getHeight();

  const availableWidthMm = pageWidthMm - marginLeft - marginRight;

  const imgWidthPx = canvas.width;
  const imgHeightPx = canvas.height;

  // px per mm mengikuti width yang dipakai di PDF
  const pxPerMm = imgWidthPx / availableWidthMm;

  // fungsi helper ambil availableHeightPx sesuai pageIndex
  const getAvailHeightPx = (pageIndex) => {
    const mt = pageIndex === 0 ? marginFirstTop : marginOtherTop;
    const mb = pageIndex === 0 ? marginFirstBottom : marginOtherBottom;
    const availableHeightMm = pageHeightMm - mt - mb;
    return Math.floor(availableHeightMm * pxPerMm);
  };

  let positionY = 0;
  let pageIndex = 0;

  while (positionY < imgHeightPx) {
    const mt = pageIndex === 0 ? marginFirstTop : marginOtherTop;
    const mb = pageIndex === 0 ? marginFirstBottom : marginOtherBottom;

    const sliceTargetHeightPx = getAvailHeightPx(pageIndex);
    const safeCutY = findSafeCutY(canvas, positionY, sliceTargetHeightPx, 180);

    // tinggi slice final
    let sliceHeightPx = safeCutY - positionY;
    if (sliceHeightPx <= 0) sliceHeightPx = Math.min(sliceTargetHeightPx, imgHeightPx - positionY);

    // bikin slice canvas
    const sliceCanvas = document.createElement("canvas");
    sliceCanvas.width = imgWidthPx;
    sliceCanvas.height = sliceHeightPx;

    const ctx = sliceCanvas.getContext("2d");
    ctx.drawImage(
      canvas,
      0,
      positionY,
      imgWidthPx,
      sliceHeightPx,
      0,
      0,
      imgWidthPx,
      sliceHeightPx
    );

    const imgData = sliceCanvas.toDataURL("image/jpeg", imageQuality);
    const sliceHeightMm = sliceHeightPx / pxPerMm;

    if (pageIndex > 0) pdf.addPage();

    // taruh dengan margin per halaman
    pdf.addImage(imgData, "JPEG", marginLeft, mt, availableWidthMm, sliceHeightMm, undefined, "FAST");

    // maju posisiY untuk halaman berikutnya (pakai overlap biar teks tidak hilang)
    const advance = Math.max(1, sliceHeightPx - overlapPx);
    positionY += advance;
    pageIndex++;
  }

  return pdf;
}

async function downloadAsPDF() {
  const content = document.getElementById("content");
  if (!content) return;

  const cleanup = applyExportMask(content);
  const canvas = await html2canvas(content, { scale: 1.5, useCORS: true, allowTaint: true });
  cleanup();

  // PAGE 1 margin normal, PAGE 2 dst margin besar
  const pdf = buildPdfFromCanvasPaged(canvas, {
    marginFirstTop: 10,
    marginFirstBottom: 15,
    marginOtherTop: 28,      // <= kamu bisa besarin lagi
    marginOtherBottom: 28,   // <= kamu bisa besarin lagi
    marginLeft: 8,
    marginRight: 8,
    overlapPx: 40,           // <= semakin besar, makin aman (tapi ada duplikasi kecil)
    imageQuality: 0.85,
  });

  pdf.save("cv.pdf");
}

// Email & Print
function sendByEmail() {
  const emailBody = "Silakan temukan CV terlampir di bawah ini.";
  const mailtoLink = `mailto:?subject=CV&body=${encodeURIComponent(emailBody)}`;
  window.location.href = mailtoLink;
}
function printCV() { window.print(); }

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
async function savePdfToDashboard() {
  const btn = document.getElementById("saveToDashboardBtn");
  if (!btn) return;

  setBtnLoading(btn, true, "Saving...");

  try {
    const content = document.getElementById("content");
    if (!content) return;

    const cleanup = applyExportMask(content);
    const canvas = await html2canvas(content, { scale: 1.5, useCORS: true, allowTaint: true });
    cleanup();

    // SAMAKAN DENGAN DOWNLOAD: page 1 normal, page 2 dst lega
    const pdf = buildPdfFromCanvasPaged(canvas, {
      marginFirstTop: 10,
      marginFirstBottom: 15,
      marginOtherTop: 28,
      marginOtherBottom: 28,
      marginLeft: 8,
      marginRight: 8,
      overlapPx: 40,
      imageQuality: 0.85,
    });

    const pdfBlob = pdf.output("blob");

    const formData = new FormData();
    formData.append("cv_file", pdfBlob, "cv.pdf");

    const templateId = btn.dataset.templateId;
    if (!templateId) {
      alert("Template ID tidak ditemukan!");
      return;
    }
    formData.append("template_id", templateId);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
    formData.append("_token", csrfToken);

    const res = await fetch("/curriculum-vitae/save", {
      method: "POST",
      headers: { "X-CSRF-TOKEN": csrfToken },
      body: formData,
    });

    const text = await res.text();
    let data;
    try { data = JSON.parse(text); }
    catch (err) {
      console.error("Server returned non-JSON response:", text);
      alert("Server error — Response bukan JSON.");
      return;
    }

    if (data.success) {
      const newId = (data.data && data.data.id) || data.new_cv_id;
      if (newId) {
        localStorage.setItem("cv_just_saved_id", String(newId));
        localStorage.setItem("cv_just_saved_at", String(Date.now()));
      }
      if (window.Swal) {
        await Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: data.message || "CV berhasil disimpan",
          timer: 1500,
          showConfirmButton: false,
        });
      } else {
        alert("CV berhasil disimpan");
      }
    } else {
      const msg = data.message || JSON.stringify(data.errors) || "Unknown error";
      if (window.Swal) Swal.fire({ icon: "error", title: "Gagal", text: msg });
      else alert("Gagal menyimpan CV: " + msg);
    }
  } catch (err) {
    console.error("JS exception", err);
    alert("Terjadi error saat proses penyimpanan (lihat console).");
  } finally {
    setBtnLoading(document.getElementById("saveToDashboardBtn"), false);
  }
}

// Event Binding
document.addEventListener("DOMContentLoaded", () => {
  const btnSave = document.getElementById("saveToDashboardBtn");
  if (btnSave) btnSave.addEventListener("click", savePdfToDashboard);
});
