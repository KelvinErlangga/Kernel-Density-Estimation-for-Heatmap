
// ===============================
// Helper
// ===============================
function hexToRgb(hex) {
    hex = hex.replace(/^#/, "");
    if (hex.length === 3) hex = hex.split("").map(x => x + x).join("");
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
    // cari target panel
    let panel = document.querySelector(".left-panel");
    if (!panel) {
        panel = document.getElementById("content"); // fallback kalau left-panel tidak ada
    }
    if (!panel) return;

    // set background
    panel.style.backgroundColor = color;

    // hitung brightness
    const rgb = hexToRgb(color);
    const brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;

    // set text color otomatis
    const textColor = brightness > 128 ? "#000000" : "#FFFFFF";
    panel.style.color = textColor;
    panel.querySelectorAll("p, h1, h2, h3, a, span, div, li").forEach(el => {
        el.style.color = textColor;
    });
}


// ===============================
// Download CV
// ===============================
async function downloadAsImage(type) {
    const content = document.getElementById("content");
    if (!content) return;

    const canvas = await html2canvas(content, { scale: 2 });
    const image = canvas.toDataURL(`image/${type}`);
    const link = document.createElement("a");
    link.href = image;
    link.download = `cv.${type}`;
    link.click();
}

async function downloadAsPDF() {
    const content = document.getElementById("content");
    if (!content) return;

    // 🔥 Sembunyikan border sementara sebelum capture
    const editableFields = content.querySelectorAll(".inline-edit"); // <-- semua inline-edit
    const previousBorders = [];
    editableFields.forEach((el, i) => {
        previousBorders[i] = el.style.border;
        el.style.border = "none";
    });

    // Capture canvas
    const canvas = await html2canvas(content, { scale: 2, useCORS: true, allowTaint: true });

    // Restore border kembali
    editableFields.forEach((el, i) => {
        el.style.border = previousBorders[i];
    });

    const imgWidthPx = canvas.width;
    const imgHeightPx = canvas.height;

    const pdf = new jspdf.jsPDF("p", "mm", "a4");
    const pageWidthMm = pdf.internal.pageSize.getWidth();
    const pageHeightMm = pdf.internal.pageSize.getHeight();

    const marginTop = 10, marginBottom = 15, marginLeft = 8, marginRight = 8;
    const availableWidthMm = pageWidthMm - marginLeft - marginRight;
    const availableHeightMm = pageHeightMm - marginTop - marginBottom;

    const imgProps = pdf.getImageProperties(canvas);
    const pdfHeight = (imgProps.height * availableWidthMm) / imgProps.width;

    if (pdfHeight <= availableHeightMm) {
        const imgData = canvas.toDataURL("image/png");
        pdf.addImage(imgData, "PNG", marginLeft, marginTop, availableWidthMm, pdfHeight);
    } else {
        const pxPerMm = imgWidthPx / pageWidthMm;
        const sliceHeightPx = Math.floor(availableHeightMm * pxPerMm);

        let positionY = 0, pageIndex = 0;
        while (positionY < imgHeightPx) {
            const currentSliceHeightPx = Math.min(sliceHeightPx, imgHeightPx - positionY);
            const sliceCanvas = document.createElement("canvas");
            sliceCanvas.width = imgWidthPx;
            sliceCanvas.height = currentSliceHeightPx;
            const ctx = sliceCanvas.getContext("2d");

            ctx.drawImage(
                canvas,
                0, positionY,
                imgWidthPx, currentSliceHeightPx,
                0, 0,
                imgWidthPx, currentSliceHeightPx
            );

            const imgData = sliceCanvas.toDataURL("image/png");
            const sliceHeightMm = currentSliceHeightPx / pxPerMm;

            if (pageIndex > 0) pdf.addPage();

            pdf.addImage(imgData, "PNG", marginLeft, marginTop, availableWidthMm, sliceHeightMm);

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
    const mailtoLink = `mailto:?subject=CV&body=${encodeURIComponent(emailBody)}`;
    window.location.href = mailtoLink;
}

function printCV() {
    window.print();
}

// ===============================
// Save to Dashboard
// ===============================
async function savePdfToDashboard() {
    const btn = document.getElementById('saveToDashboardBtn');
    if (!btn) return;

    btn.disabled = true;
    btn.innerText = 'Saving...';

    try {
        const content = document.getElementById('content');
        const canvas = await html2canvas(content, { scale: 2, useCORS: true, allowTaint: true });
        const imgData = canvas.toDataURL('image/png');

        const pdf = new jspdf.jsPDF('p','mm','a4');
        const pageWidth = pdf.internal.pageSize.getWidth();
        const imgProps = pdf.getImageProperties(imgData);
        const pdfHeight = (imgProps.height * pageWidth) / imgProps.width;
        pdf.addImage(imgData, 'PNG', 0, 0, pageWidth, pdfHeight);

        const pdfBlob = pdf.output('blob');

        const formData = new FormData();
        formData.append('cv_file', pdfBlob, 'cv.pdf');

        const templateId = btn.dataset.templateId;
        if (!templateId) {
            alert('Template ID tidak ditemukan!');
            btn.disabled = false;
            btn.innerText = 'Save to Dashboard';
            return;
        }
        formData.append('template_id', templateId);

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrfToken);

        const res = await fetch("{{ route('pelamar.curriculum_vitae.save') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        });

        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            console.error('Server returned non-JSON response:', text);
            alert('Server error — Response bukan JSON.');
            return;
        }

        if (data.success) {
            if (window.Swal) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1800, showConfirmButton: false });
            } else {
                alert('CV berhasil disimpan');
            }
        } else {
            const msg = (data.message || JSON.stringify(data.errors) || 'Unknown error');
            if (window.Swal) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
            } else {
                alert('Gagal menyimpan CV: ' + msg);
            }
        }
    } catch (err) {
        console.error('JS exception', err);
        alert('Terjadi error saat proses penyimpanan (lihat console).');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Save to Dashboard';
    }
}

// ===============================
// Event Binding
// ===============================
document.addEventListener("DOMContentLoaded", () => {
    const btnSave = document.getElementById('saveToDashboardBtn');
    if (btnSave) btnSave.addEventListener('click', savePdfToDashboard);
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.editable').forEach(function (el) {
        el.addEventListener('blur', function () {
            let cv_id   = this.dataset.cv;
            let section = this.dataset.section;
            let id      = this.dataset.id || null;
            let field   = this.dataset.field;
            let value   = this.innerText.trim();

            fetch("{{ route('cv.update.inline') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    cv_id: cv_id,
                    section: section,
                    id: id,
                    field: field,
                    value: value
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (!data.success) {
                    alert(data.message || "Gagal update data");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Terjadi error saat menyimpan");
            });
        });
    });
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
});

// Handler contenteditable
$(document).on('blur', '[contenteditable="true"]', function () {
    const $el = $(this);

    $.ajax({
        url: '/cv/update-inline',
        method: 'POST',
        data: {
            cv_id: $el.data('cv'),
            section: $el.data('section'),
            id: $el.data('id'),       // <-- penting untuk experiences
            field: $el.data('field'),
            value: $el.text().trim()
        },
        success: function (res) {
            console.log("Success:", res);
        },
        error: function (xhr) {
            console.error("Error saving inline edit:", xhr.responseText);
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // helper: get CSRF token
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    document.querySelectorAll('.inline-edit').forEach(el => {
        // optionally show outline while editing
        el.addEventListener('focus', () => {
            el.dataset._old = el.innerHTML; // for possible revert
            el.style.outline = '1px dashed #999';
        });

        el.addEventListener('blur', function () {
            el.style.outline = 'none';
            const cv_id = this.dataset.cv;
            const section = this.dataset.section;
            const id = this.dataset.id || null;
            const field = this.dataset.field;
            // for description we prefer innerHTML (to keep lists/lines), for simple fields use text
            const value = (field === 'description_experience') ? this.innerHTML.trim() : this.innerText.trim();

            // quick guard
            if (!cv_id || !section || !field) {
                console.error('Missing data attributes on editable element', {cv_id, section, field});
                return;
            }

            fetch('/cv/update-inline', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    cv_id: cv_id,
                    section: section,
                    id: id,
                    field: field,
                    value: value
                })
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    console.warn('Update failed:', res);
                    // optional: restore previous content
                    // this.innerHTML = this.dataset._old ?? this.innerHTML;
                    alert(res.message || 'Gagal menyimpan perubahan');
                } else {
                    // success - optionally show small toast
                    console.log('Updated', field, '->', value);
                }
            })
            .catch(err => {
                console.error('Request error', err);
                alert('Terjadi error saat menyimpan perubahan (cek console).');
            });
        });
    });
});
