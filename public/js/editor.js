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

async function downloadAsPDF() {
  const content = document.getElementById("content");
  if (!content) return;

  const cleanup = applyExportMask(content);
  // Gunakan scale 1.5 (cukup tajam, file kecil)
  const canvas = await html2canvas(content, { scale: 1.5, useCORS: true, allowTaint: true });
  cleanup();

  const imgWidthPx = canvas.width;
  const imgHeightPx = canvas.height;

  const pdf = new jspdf.jsPDF("p", "mm", "a4");
  const pageWidthMm  = pdf.internal.pageSize.getWidth();
  const pageHeightMm = pdf.internal.pageSize.getHeight();

  const marginTop = 10, marginBottom = 15, marginLeft = 8, marginRight = 8;
  const availableWidthMm  = pageWidthMm  - marginLeft - marginRight;
  const availableHeightMm = pageHeightMm - marginTop  - marginBottom;

  const imgProps = pdf.getImageProperties(canvas);
  const pdfHeight = (imgProps.height * availableWidthMm) / imgProps.width;

  if (pdfHeight <= availableHeightMm) {
    // Ubah ke JPEG kualitas 0.8 (80%)
    const imgData = canvas.toDataURL("image/jpeg", 0.8);
    pdf.addImage(imgData, "JPEG", marginLeft, marginTop, availableWidthMm, pdfHeight, undefined, 'FAST');
  } else {
    // Logika untuk memotong gambar jika lebih dari 1 halaman
    const pxPerMm = imgWidthPx / pageWidthMm;
    const sliceHeightPx = Math.floor(availableHeightMm * pxPerMm);
    let positionY = 0, pageIndex = 0;

    while (positionY < imgHeightPx) {
      const currentSliceHeightPx = Math.min(sliceHeightPx, imgHeightPx - positionY);
      const sliceCanvas = document.createElement("canvas");
      sliceCanvas.width  = imgWidthPx;
      sliceCanvas.height = currentSliceHeightPx;
      const ctx = sliceCanvas.getContext("2d");

      ctx.drawImage(canvas, 0, positionY, imgWidthPx, currentSliceHeightPx, 0, 0, imgWidthPx, currentSliceHeightPx);

      const imgData = sliceCanvas.toDataURL("image/jpeg", 0.8); // JPEG
      const sliceHeightMm = currentSliceHeightPx / pxPerMm;

      if (pageIndex > 0) pdf.addPage();
      pdf.addImage(imgData, "JPEG", marginLeft, marginTop, availableWidthMm, sliceHeightMm, undefined, 'FAST'); // JPEG

      positionY += currentSliceHeightPx;
      pageIndex++;
    }
  }

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

    // SVG spinner kecil (tanpa dependensi)
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
      btn.textContent = "Save to Dashboard";
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

    const cleanup = applyExportMask(content);
    // Gunakan scale 1.5
    const canvas = await html2canvas(content, { scale: 1.5, useCORS: true, allowTaint: true });
    cleanup();

    // Ubah ke JPEG kualitas 0.8 (80%)
    const imgData = canvas.toDataURL("image/jpeg", 0.8);

    const pdf = new jspdf.jsPDF("p", "mm", "a4");
    const pageWidth = pdf.internal.pageSize.getWidth();
    const imgProps = pdf.getImageProperties(imgData);
    const pdfHeight = (imgProps.height * pageWidth) / imgProps.width;

    // Tambahkan gambar JPEG
    pdf.addImage(imgData, "JPEG", 0, 0, pageWidth, pdfHeight, undefined, 'FAST');

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
