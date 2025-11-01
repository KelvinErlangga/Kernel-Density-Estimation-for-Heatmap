"use strict";

// ===============================
// Helper
// ===============================
function hexToRgb(hex) {
  hex = hex.replace(/^#/, "");
  if (hex.length === 3) hex = hex.split("").map(function (x) {
    return x + x;
  }).join("");
  var num = parseInt(hex, 16);
  return {
    r: num >> 16 & 255,
    g: num >> 8 & 255,
    b: num & 255
  };
} // ===============================
// Font & Background
// ===============================


function changeFont(selectElement) {
  var content = document.getElementById("content");
  if (content) content.style.fontFamily = selectElement.value;
}

function changeBackgroundColor(color) {
  var panel = document.querySelector(".left-panel") || document.getElementById("content");
  if (!panel) return;
  panel.style.backgroundColor = color;
  var rgb = hexToRgb(color);
  var brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
  var textColor = brightness > 128 ? "#000000" : "#FFFFFF";
  panel.style.color = textColor;
  panel.querySelectorAll("p,h1,h2,h3,a,span,div,li").forEach(function (el) {
    el.style.color = textColor;
  });
} // ===== Export mask: hilangkan outline/border saat snapshot =====


function applyExportMask(root) {
  if (!root) return function () {};
  var styleEl = document.createElement("style");
  styleEl.id = "export-mask-style";
  styleEl.textContent = "\n    #content.exporting .inline-edit:empty:before { content: \"\" !important; }\n    #content.exporting .export-hidden,\n    #content.exporting .drag-indicator,\n    #content.exporting .delete-indicator { display: none !important; }\n  ";
  document.head.appendChild(styleEl);
  root.classList.add("exporting"); // strip border/outline/pill seperti sebelumnya …

  var toClean = [];
  root.querySelectorAll("*").forEach(function (el) {
    var cs = window.getComputedStyle(el);
    var hasDashed = ["borderTopStyle", "borderRightStyle", "borderBottomStyle", "borderLeftStyle"].some(function (k) {
      return cs[k] === "dashed";
    });

    if (hasDashed || el.classList.contains("inline-edit") || el.classList.contains("pill")) {
      toClean.push({
        el: el,
        border: el.style.border,
        borderTop: el.style.borderTop,
        borderRight: el.style.borderRight,
        borderBottom: el.style.borderBottom,
        borderLeft: el.style.borderLeft,
        outline: el.style.outline,
        boxShadow: el.style.boxShadow
      });
      el.style.border = el.style.borderTop = el.style.borderRight = el.style.borderBottom = el.style.borderLeft = "none";
      el.style.outline = el.style.boxShadow = "none";
    }
  }); // pastikan elemen yang wajib hilang benar2 disembunyikan (backup display)

  var hiddenEls = Array.from(root.querySelectorAll(".export-hidden, .drag-indicator, .delete-indicator"));
  hiddenEls.forEach(function (el) {
    el.dataset._display = el.style.display;
    el.style.display = "none";
  });
  return function () {
    toClean.forEach(function (s) {
      s.el.style.border = s.border;
      s.el.style.borderTop = s.borderTop;
      s.el.style.borderRight = s.borderRight;
      s.el.style.borderBottom = s.borderBottom;
      s.el.style.borderLeft = s.borderLeft;
      s.el.style.outline = s.outline;
      s.el.style.boxShadow = s.boxShadow;
    });
    hiddenEls.forEach(function (el) {
      el.style.display = el.dataset._display || "";
      delete el.dataset._display;
    });
    root.classList.remove("exporting");
    styleEl.remove();
  };
} // ===============================
// Download CV (Image / PDF)
// ===============================


function downloadAsImage(type) {
  var content, cleanup, canvas, image, link;
  return regeneratorRuntime.async(function downloadAsImage$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          content = document.getElementById("content");

          if (content) {
            _context.next = 3;
            break;
          }

          return _context.abrupt("return");

        case 3:
          cleanup = applyExportMask(content);
          _context.next = 6;
          return regeneratorRuntime.awrap(html2canvas(content, {
            scale: 2,
            useCORS: true,
            allowTaint: true
          }));

        case 6:
          canvas = _context.sent;
          cleanup();
          image = canvas.toDataURL("image/".concat(type));
          link = document.createElement("a");
          link.href = image;
          link.download = "cv.".concat(type);
          link.click();

        case 13:
        case "end":
          return _context.stop();
      }
    }
  });
}

function downloadAsPDF() {
  var content, cleanup, canvas, imgWidthPx, imgHeightPx, pdf, pageWidthMm, pageHeightMm, marginTop, marginBottom, marginLeft, marginRight, availableWidthMm, availableHeightMm, imgProps, pdfHeight, imgData, pxPerMm, sliceHeightPx, positionY, pageIndex, currentSliceHeightPx, sliceCanvas, ctx, _imgData, sliceHeightMm;

  return regeneratorRuntime.async(function downloadAsPDF$(_context2) {
    while (1) {
      switch (_context2.prev = _context2.next) {
        case 0:
          content = document.getElementById("content");

          if (content) {
            _context2.next = 3;
            break;
          }

          return _context2.abrupt("return");

        case 3:
          cleanup = applyExportMask(content);
          _context2.next = 6;
          return regeneratorRuntime.awrap(html2canvas(content, {
            scale: 2,
            useCORS: true,
            allowTaint: true
          }));

        case 6:
          canvas = _context2.sent;
          cleanup();
          imgWidthPx = canvas.width;
          imgHeightPx = canvas.height;
          pdf = new jspdf.jsPDF("p", "mm", "a4");
          pageWidthMm = pdf.internal.pageSize.getWidth();
          pageHeightMm = pdf.internal.pageSize.getHeight();
          marginTop = 10, marginBottom = 15, marginLeft = 8, marginRight = 8;
          availableWidthMm = pageWidthMm - marginLeft - marginRight;
          availableHeightMm = pageHeightMm - marginTop - marginBottom;
          imgProps = pdf.getImageProperties(canvas);
          pdfHeight = imgProps.height * availableWidthMm / imgProps.width;

          if (pdfHeight <= availableHeightMm) {
            imgData = canvas.toDataURL("image/png");
            pdf.addImage(imgData, "PNG", marginLeft, marginTop, availableWidthMm, pdfHeight);
          } else {
            pxPerMm = imgWidthPx / pageWidthMm;
            sliceHeightPx = Math.floor(availableHeightMm * pxPerMm);
            positionY = 0, pageIndex = 0;

            while (positionY < imgHeightPx) {
              currentSliceHeightPx = Math.min(sliceHeightPx, imgHeightPx - positionY);
              sliceCanvas = document.createElement("canvas");
              sliceCanvas.width = imgWidthPx;
              sliceCanvas.height = currentSliceHeightPx;
              ctx = sliceCanvas.getContext("2d");
              ctx.drawImage(canvas, 0, positionY, imgWidthPx, currentSliceHeightPx, 0, 0, imgWidthPx, currentSliceHeightPx);
              _imgData = sliceCanvas.toDataURL("image/png");
              sliceHeightMm = currentSliceHeightPx / pxPerMm;
              if (pageIndex > 0) pdf.addPage();
              pdf.addImage(_imgData, "PNG", marginLeft, marginTop, availableWidthMm, sliceHeightMm);
              positionY += currentSliceHeightPx;
              pageIndex++;
            }
          }

          pdf.save("cv.pdf");

        case 20:
        case "end":
          return _context2.stop();
      }
    }
  });
} // ===============================
// Email & Print
// ===============================


function sendByEmail() {
  var emailBody = "Silakan temukan CV terlampir di bawah ini.";
  var mailtoLink = "mailto:?subject=CV&body=".concat(encodeURIComponent(emailBody));
  window.location.href = mailtoLink;
}

function printCV() {
  window.print();
} // ===============================
// Save to Dashboard (PDF upload)
// ===============================


function savePdfToDashboard() {
  var btn, content, cleanup, canvas, imgData, pdf, pageWidth, imgProps, pdfHeight, pdfBlob, formData, templateId, csrfToken, res, text, data, msg;
  return regeneratorRuntime.async(function savePdfToDashboard$(_context3) {
    while (1) {
      switch (_context3.prev = _context3.next) {
        case 0:
          btn = document.getElementById("saveToDashboardBtn");

          if (btn) {
            _context3.next = 3;
            break;
          }

          return _context3.abrupt("return");

        case 3:
          btn.disabled = true;
          btn.innerText = "Saving...";
          _context3.prev = 5;
          content = document.getElementById("content");
          cleanup = applyExportMask(content);
          _context3.next = 10;
          return regeneratorRuntime.awrap(html2canvas(content, {
            scale: 2,
            useCORS: true,
            allowTaint: true
          }));

        case 10:
          canvas = _context3.sent;
          cleanup();
          imgData = canvas.toDataURL("image/png");
          pdf = new jspdf.jsPDF("p", "mm", "a4");
          pageWidth = pdf.internal.pageSize.getWidth();
          imgProps = pdf.getImageProperties(imgData);
          pdfHeight = imgProps.height * pageWidth / imgProps.width;
          pdf.addImage(imgData, "PNG", 0, 0, pageWidth, pdfHeight);
          pdfBlob = pdf.output("blob");
          formData = new FormData();
          formData.append("cv_file", pdfBlob, "cv.pdf");
          templateId = btn.dataset.templateId;

          if (templateId) {
            _context3.next = 27;
            break;
          }

          alert("Template ID tidak ditemukan!");
          btn.disabled = false;
          btn.innerText = "Save to Dashboard";
          return _context3.abrupt("return");

        case 27:
          formData.append("template_id", templateId);
          csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
          formData.append("_token", csrfToken);
          _context3.next = 32;
          return regeneratorRuntime.awrap(fetch("/curriculum-vitae/save", {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": csrfToken
            },
            body: formData
          }));

        case 32:
          res = _context3.sent;
          _context3.next = 35;
          return regeneratorRuntime.awrap(res.text());

        case 35:
          text = _context3.sent;
          _context3.prev = 36;
          data = JSON.parse(text);
          _context3.next = 45;
          break;

        case 40:
          _context3.prev = 40;
          _context3.t0 = _context3["catch"](36);
          console.error("Server returned non-JSON response:", text);
          alert("Server error — Response bukan JSON.");
          return _context3.abrupt("return");

        case 45:
          if (data.success) {
            if (window.Swal) Swal.fire({
              icon: "success",
              title: "Berhasil",
              text: data.message,
              timer: 1800,
              showConfirmButton: false
            });else alert("CV berhasil disimpan");
          } else {
            msg = data.message || JSON.stringify(data.errors) || "Unknown error";
            if (window.Swal) Swal.fire({
              icon: "error",
              title: "Gagal",
              text: msg
            });else alert("Gagal menyimpan CV: " + msg);
          }

          _context3.next = 52;
          break;

        case 48:
          _context3.prev = 48;
          _context3.t1 = _context3["catch"](5);
          console.error("JS exception", _context3.t1);
          alert("Terjadi error saat proses penyimpanan (lihat console).");

        case 52:
          _context3.prev = 52;
          btn.disabled = false;
          btn.innerText = "Save to Dashboard";
          return _context3.finish(52);

        case 56:
        case "end":
          return _context3.stop();
      }
    }
  }, null, null, [[5, 48, 52, 56], [36, 40]]);
} // ===============================
// Event Binding
// ===============================


document.addEventListener("DOMContentLoaded", function () {
  var btnSave = document.getElementById("saveToDashboardBtn");
  if (btnSave) btnSave.addEventListener("click", savePdfToDashboard);
});