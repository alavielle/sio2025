<html>
  <head>
    <meta charset="utf-8" />
    <script src="https://unpkg.com/pdf-lib"></script>
  </head>

  <body>
    <iframe id="pdf" style="width: 21cm; height: 30cm;"></iframe>
  </body>

  <script>
    createPdf();
    async function createPdf() {
      const pdfDoc = await PDFLib.PDFDocument.create();
      const page = pdfDoc.addPage();
      page.moveTo(110, 200);
      page.drawText('Hello World!');
      const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
      document.getElementById('pdf').src = pdfDataUri;
    }
  </script>
</html>