"use strict";

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
} // Font & Background


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
  styleEl.textContent = "\n        #content.exporting .inline-edit:empty:before { content: \"\" !important; }\n        #content.exporting .export-hidden,\n        #content.exporting .drag-indicator,\n        #content.exporting .delete-indicator { display: none !important; }\n\n        #content.exporting .cv-link,\n        #content.exporting .cv-link:visited,\n        #content.exporting .cv-link:hover,\n        #content.exporting .cv-link:active{\n            color:#2563eb !important;\n            text-decoration:none !important;\n            position:relative !important;\n            display:inline-block !important;\n            padding-bottom:2px !important;\n        }\n\n        #content.exporting .cv-link::after{\n            content:\"\" !important;\n            position:absolute !important;\n            left:0 !important;\n            right:0 !important;\n            bottom:0 !important;\n            height:1px !important;\n            background:#2563eb !important;\n            border-radius:0 !important;\n        }\n\n        #content.exporting .cv-link *{\n            color:inherit !important;\n            text-decoration:none !important;\n        }\n    ";
  document.head.appendChild(styleEl);
  root.classList.add("exporting");
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
  });
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
} // Download CV (Image / PDF)


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

function findSafeCutY(canvas, startY, targetHeight) {
  var scanBackPx = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 360;
  var opts = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : {};
  var _opts$safetyPadPx = opts.safetyPadPx,
      safetyPadPx = _opts$safetyPadPx === void 0 ? 26 : _opts$safetyPadPx,
      _opts$consecutiveRows = opts.consecutiveRows,
      consecutiveRows = _opts$consecutiveRows === void 0 ? 10 : _opts$consecutiveRows,
      _opts$minBlankRatio = opts.minBlankRatio,
      minBlankRatio = _opts$minBlankRatio === void 0 ? 0.985 : _opts$minBlankRatio,
      _opts$colorTol = opts.colorTol,
      colorTol = _opts$colorTol === void 0 ? 18 : _opts$colorTol;
  var w = canvas.width;
  var h = canvas.height;
  var idealEndY = Math.min(startY + targetHeight, h);
  if (idealEndY <= startY) return idealEndY;
  var ctx = canvas.getContext("2d", {
    willReadFrequently: true
  }); // ambil warna bg referensi

  var bg = ctx.getImageData(4, Math.min(startY + 4, h - 1), 1, 1).data;
  var bgR = bg[0],
      bgG = bg[1],
      bgB = bg[2];
  var scanTop = Math.max(startY + Math.floor(targetHeight * 0.72), idealEndY - scanBackPx);
  if (scanTop >= idealEndY) return idealEndY;
  var scanHeight = idealEndY - scanTop;
  var img = ctx.getImageData(0, scanTop, w, scanHeight).data; // sampling lebih rapat supaya tidak miss teks

  var xStep = Math.max(6, Math.floor(w / 520));

  function isBgPixel(r, g, b, a) {
    if (a < 10) return true;
    return Math.abs(r - bgR) <= colorTol && Math.abs(g - bgG) <= colorTol && Math.abs(b - bgB) <= colorTol;
  } // cek baris kosong PER BAGIAN: kiri & kanan (hindari false-blank karena tengah putih)


  function blankRatioForRange(rowOffset, xStart, xEnd) {
    var blank = 0,
        total = 0;

    for (var x = xStart; x < xEnd; x += xStep) {
      var idx = rowOffset + x * 4;
      var r = img[idx],
          g = img[idx + 1],
          b = img[idx + 2],
          a = img[idx + 3];
      total++;
      if (isBgPixel(r, g, b, a)) blank++;
    }

    return blank / total;
  }

  var leftStart = Math.floor(w * 0.06);
  var leftEnd = Math.floor(w * 0.47);
  var rightStart = Math.floor(w * 0.53);
  var rightEnd = Math.floor(w * 0.94);
  var run = 0;

  for (var localY = scanHeight - 1; localY >= 0; localY--) {
    var rowOffset = localY * w * 4;
    var leftBlank = blankRatioForRange(rowOffset, leftStart, leftEnd);
    var rightBlank = blankRatioForRange(rowOffset, rightStart, rightEnd); // baris dianggap kosong hanya jika KIRI & KANAN sama-sama kosong

    if (leftBlank >= minBlankRatio && rightBlank >= minBlankRatio) {
      run++;

      if (run >= consecutiveRows) {
        var cut = scanTop + localY - safetyPadPx;
        return Math.max(startY + 1, Math.min(cut, idealEndY));
      }
    } else {
      run = 0;
    }
  }

  return idealEndY;
}

function buildPdfFromCanvasPaged(canvas) {
  var opts = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var _opts$marginFirstTop = opts.marginFirstTop,
      marginFirstTop = _opts$marginFirstTop === void 0 ? 10 : _opts$marginFirstTop,
      _opts$marginFirstBott = opts.marginFirstBottom,
      marginFirstBottom = _opts$marginFirstBott === void 0 ? 15 : _opts$marginFirstBott,
      _opts$marginOtherTop = opts.marginOtherTop,
      marginOtherTop = _opts$marginOtherTop === void 0 ? 28 : _opts$marginOtherTop,
      _opts$marginOtherBott = opts.marginOtherBottom,
      marginOtherBottom = _opts$marginOtherBott === void 0 ? 28 : _opts$marginOtherBott,
      _opts$marginLeft = opts.marginLeft,
      marginLeft = _opts$marginLeft === void 0 ? 8 : _opts$marginLeft,
      _opts$marginRight = opts.marginRight,
      marginRight = _opts$marginRight === void 0 ? 8 : _opts$marginRight,
      _opts$overlapPx = opts.overlapPx,
      overlapPx = _opts$overlapPx === void 0 ? 70 : _opts$overlapPx,
      _opts$imageQuality = opts.imageQuality,
      imageQuality = _opts$imageQuality === void 0 ? 0.9 : _opts$imageQuality;
  var pdf = new jspdf.jsPDF("p", "mm", "a4");
  var pageWidthMm = pdf.internal.pageSize.getWidth();
  var pageHeightMm = pdf.internal.pageSize.getHeight();
  var availableWidthMm = pageWidthMm - marginLeft - marginRight;
  var imgWidthPx = canvas.width;
  var imgHeightPx = canvas.height;
  var pxPerMm = imgWidthPx / availableWidthMm;

  var getAvailHeightPx = function getAvailHeightPx(pageIndex) {
    var mt = pageIndex === 0 ? marginFirstTop : marginOtherTop;
    var mb = pageIndex === 0 ? marginFirstBottom : marginOtherBottom;
    return Math.floor((pageHeightMm - mt - mb) * pxPerMm);
  };

  var y = 0;
  var pageIndex = 0;
  var maxPagesGuard = 12;

  while (y < imgHeightPx && pageIndex < maxPagesGuard) {
    var mt = pageIndex === 0 ? marginFirstTop : marginOtherTop;
    var targetH = getAvailHeightPx(pageIndex);
    var remaining = imgHeightPx - y; // last page

    if (remaining <= targetH + 2) {
      var _sliceCanvas = document.createElement("canvas");

      _sliceCanvas.width = imgWidthPx;
      _sliceCanvas.height = remaining;

      _sliceCanvas.getContext("2d").drawImage(canvas, 0, y, imgWidthPx, remaining, 0, 0, imgWidthPx, remaining);

      var _imgData = _sliceCanvas.toDataURL("image/jpeg", imageQuality);

      var _sliceHeightMm = remaining / pxPerMm;

      if (pageIndex > 0) pdf.addPage();
      pdf.addImage(_imgData, "JPEG", marginLeft, mt, availableWidthMm, _sliceHeightMm, undefined, "FAST");
      break;
    } // cari safe cut + padding


    var safeCutY = findSafeCutY(canvas, y, targetH, 360, {
      safetyPadPx: 26,
      consecutiveRows: 10,
      minBlankRatio: 0.985,
      colorTol: 18
    });
    var sliceH = safeCutY - y; // jangan sampai slice terlalu pendek

    var minSlice = Math.floor(targetH * 0.88); // dinaikkan dari 0.82 -> 0.88

    if (sliceH < minSlice) sliceH = targetH;
    sliceH = Math.min(sliceH, remaining);
    var sliceCanvas = document.createElement("canvas");
    sliceCanvas.width = imgWidthPx;
    sliceCanvas.height = sliceH;
    sliceCanvas.getContext("2d").drawImage(canvas, 0, y, imgWidthPx, sliceH, 0, 0, imgWidthPx, sliceH);
    var imgData = sliceCanvas.toDataURL("image/jpeg", imageQuality);
    var sliceHeightMm = sliceH / pxPerMm;
    if (pageIndex > 0) pdf.addPage();
    pdf.addImage(imgData, "JPEG", marginLeft, mt, availableWidthMm, sliceHeightMm, undefined, "FAST"); // advance aman

    var advance = Math.max(minSlice, sliceH - overlapPx);
    y += advance;
    pageIndex++;
  }

  return pdf;
} // async function downloadAsPDF() {
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


function downloadAsPDF() {
  var lang = localStorage.getItem("cv_lang") || "id";
  window.location.href = "{{ route('export-cv.pdf.text', $cv->id) }}?lang=".concat(encodeURIComponent(lang));
} // Email & Print


function sendByEmail() {
  var emailBody = "Silakan temukan CV terlampir di bawah ini.";
  var mailtoLink = "mailto:?subject=CV&body=".concat(encodeURIComponent(emailBody));
  window.location.href = mailtoLink;
}

function printCV() {
  window.print();
} // Spinner Button Helpers


function ensureSpinnerCss() {
  if (document.getElementById("btn-spinner-style")) return;
  var css = document.createElement("style");
  css.id = "btn-spinner-style";
  css.textContent = "\n    @keyframes spin360 { to { transform: rotate(360deg); } }\n    .btn-loading { pointer-events: none; opacity: .9; }\n    .btn-loading .spinner {\n      width: 16px; height: 16px; margin-right: 8px;\n      display: inline-block; vertical-align: -2px;\n    }\n    .btn-loading .spinner svg { width: 16px; height: 16px; animation: spin360 1s linear infinite; }\n  ";
  document.head.appendChild(css);
}

function setBtnLoading(btn, isLoading) {
  var textWhenLoading = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "Saving...";
  if (!btn) return;
  ensureSpinnerCss();

  if (isLoading) {
    if (!btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;
    btn.classList.add("btn-loading");
    btn.disabled = true;
    var spinnerSvg = "\n      <span class=\"spinner\" aria-hidden=\"true\">\n        <svg viewBox=\"0 0 50 50\" fill=\"none\">\n          <circle cx=\"25\" cy=\"25\" r=\"20\" stroke=\"currentColor\" stroke-opacity=\".2\" stroke-width=\"6\"/>\n          <path d=\"M45 25a20 20 0 0 1-20 20\" stroke=\"currentColor\" stroke-width=\"6\" stroke-linecap=\"round\"/>\n        </svg>\n      </span>";
    btn.innerHTML = "".concat(spinnerSvg, "<span>").concat(textWhenLoading, "</span>");
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
} // Save to Dashboard (PDF upload)
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
// Event Binding


document.addEventListener("DOMContentLoaded", function () {
  var btnSave = document.getElementById("saveToDashboardBtn");
  if (btnSave) btnSave.addEventListener("click", savePdfToDashboard);
});