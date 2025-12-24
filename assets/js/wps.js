// Initialize Quill editor with a Word-like toolbar
document.addEventListener("DOMContentLoaded", function () {
  const toolbarOptions = [
    [{ font: [] }, { size: ["small", false, "large", "huge"] }],
    ["bold", "italic", "underline", "strike"],
    [{ color: [] }, { background: [] }],
    [{ script: "sub" }, { script: "super" }],
    [{ header: [1, 2, 3, 4, 5, 6, false] }],
    [
      { list: "ordered" },
      { list: "bullet" },
      { indent: "-1" },
      { indent: "+1" },
    ],
    [{ align: [] }],
    ["blockquote", "code-block"],
    ["link", "image", "video"],
    ["clean"],
  ];

  const quill = new Quill("#editor", {
    modules: {
      toolbar: toolbarOptions,
    },
    placeholder: "Tulis catatan Anda di sini... (WPS - KaiAdmin)",
    theme: "snow",
  });

  // New document
  document.getElementById("btn-new").addEventListener("click", function () {
    if (
      confirm("Buat dokumen baru? Semua perubahan belum disimpan akan hilang.")
    ) {
      quill.setContents([{ insert: "\n" }]);
    }
  });

  // Save local HTML
  document
    .getElementById("btn-save-local")
    .addEventListener("click", function () {
      const html = editorToHTML();
      const blob = new Blob([html], { type: "text/html" });
      saveAs(blob, "note.html");
    });

  // Open HTML
  const fileOpen = document.getElementById("file-open");
  fileOpen.addEventListener("change", function (e) {
    const f = e.target.files[0];
    if (!f) return;
    const reader = new FileReader();
    reader.onload = () => {
      quill.clipboard.dangerouslyPasteHTML(reader.result);
    };
    reader.readAsText(f);
  });

  // Export DOCX
  document
    .getElementById("btn-export-docx")
    .addEventListener("click", function () {
      const html =
        '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>' +
        editorToHTML() +
        "</body></html>";
      const converted = htmlDocx.asBlob(html, { orientation: "portrait" });
      saveAs(converted, "note.docx");
    });

  // Export PDF (simple approach: render to canvas then PDF)
  document
    .getElementById("btn-export-pdf")
    .addEventListener("click", async function () {
      const editorEl = document.querySelector("#editor");
      // Expand width for better rendering
      const originalWidth = editorEl.style.width;
      editorEl.style.width = "1100px";
      const canvas = await html2canvas(editorEl, { scale: 2 });
      editorEl.style.width = originalWidth;
      const imgData = canvas.toDataURL("image/png");
      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF({
        orientation: "portrait",
        unit: "px",
        format: [canvas.width, canvas.height],
      });
      pdf.addImage(imgData, "PNG", 0, 0, canvas.width, canvas.height);
      pdf.save("note.pdf");
    });

  // Print
  document.getElementById("btn-print").addEventListener("click", function () {
    const w = window.open("", "_blank");
    w.document.write("<html><head><title>Print</title>");
    w.document.write(
      '<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">'
    );
    w.document.write("</head><body>");
    w.document.write(editorToHTML());
    w.document.write("</body></html>");
    w.document.close();
    w.focus();
    setTimeout(() => {
      w.print();
      w.close();
    }, 500);
  });

  function editorToHTML() {
    // Quill stores content as delta, but getInnerHTML via container
    return document.querySelector("#editor .ql-editor").innerHTML;
  }
});
