<?php

namespace App\Service;

use Dompdf\Dompdf;

class PdfService {

    private $domPdf;

    public function __construct()
    {
        $this->domPdf = new Dompdf();
    }

    public function showPdfFile($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream("details.pdf", [
            'Attachement' => false,
        ]);
    }

    public function generateBinaryPDF($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }
}