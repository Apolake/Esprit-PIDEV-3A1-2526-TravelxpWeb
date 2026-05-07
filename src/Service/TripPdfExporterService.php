<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class TripPdfExporterService
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /**
     * @param array<string, mixed> $report
     */
    public function renderTripReportPdf(array $report): string
    {
        $html = $this->twig->render('trip/report_pdf.html.twig', [
            'report' => $report,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->setDefaultFont('DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}

