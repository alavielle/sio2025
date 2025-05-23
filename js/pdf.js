
    async function createPdf(texte) {
      const pdfDoc = await PDFLib.PDFDocument.create();
      const page = pdfDoc.addPage();
      page.moveTo(110, 200);
      page.drawText(texte);
      const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
      document.getElementById('pdf').src = pdfDataUri;
    }