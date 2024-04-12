<?php

namespace App\Services;

use TCPDF;

class PdfService
{
    public function generatePdf($data)
    {
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        $pdf->writeHTML('<h1>Welcome to Laravel PDF!</h1>');
        $pdf->writeHTML('<p>This is a sample PDF generated using Laravel and TCPDF.</p>');
        return $pdf;
    }
}
