<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Entity\User;
use App\Service\TripPdfExporterService;
use App\Service\TripQrCodeService;
use App\Service\TripReportMailerService;
use App\Service\TripReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TripPremiumController extends AbstractController
{
    #[Route('/trips/{id}/export/pdf', name: 'trip_export_pdf', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/admin/trips/{id}/export/pdf', name: 'admin_trip_export_pdf', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function exportPdf(
        Request $request,
        Trip $trip,
        TripReportService $tripReportService,
        TripPdfExporterService $tripPdfExporterService,
    ): Response {
        $this->enforceRoleByRouteName((string) $request->attributes->get('_route'));

        $selectedCurrency = (string) $request->query->get('currency', $trip->getCurrency());
        $aiSummary = trim((string) $request->query->get('aiSummary', ''));
        $report = $tripReportService->buildTripReport($trip, $selectedCurrency, [
            'aiSummary' => $aiSummary,
        ]);

        $pdf = $tripPdfExporterService->renderTripReportPdf($report);
        $filename = sprintf('trip-report-%d.pdf', (int) $trip->getId());

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    #[Route('/trips/{id}/share/mail', name: 'trip_share_mail', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[Route('/admin/trips/{id}/share/mail', name: 'admin_trip_share_mail', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function sendMail(
        Request $request,
        Trip $trip,
        TripReportService $tripReportService,
        TripPdfExporterService $tripPdfExporterService,
        TripReportMailerService $tripReportMailerService,
    ): RedirectResponse {
        $this->enforceRoleByRouteName((string) $request->attributes->get('_route'));

        $token = (string) $request->request->get('_token', '');
        if (!$this->isCsrfTokenValid('trip_share_mail_' . $trip->getId(), $token)) {
            $this->addFlash('danger', 'Invalid CSRF token for share action.');

            return $this->redirectBack($request, $trip);
        }

        $viewer = $this->getUser();
        $sender = $viewer instanceof User ? $viewer : null;
        $defaultEmail = trim((string) ($sender?->getEmail() ?? ''));
        $overrideEmail = trim((string) $request->request->get('recipient_email', ''));
        $email = $overrideEmail !== '' ? $overrideEmail : $defaultEmail;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'No valid default email found. Add an override email to send the report.');

            return $this->redirectBack($request, $trip);
        }

        $selectedCurrency = (string) $request->request->get('currency', $trip->getCurrency());
        $aiSummary = trim((string) $request->request->get('ai_summary', ''));
        $customMessage = trim((string) $request->request->get('custom_message', ''));
        $report = $tripReportService->buildTripReport($trip, $selectedCurrency, [
            'aiSummary' => $aiSummary,
        ]);
        $pdf = $tripPdfExporterService->renderTripReportPdf($report);
        $filename = sprintf('trip-report-%d.pdf', (int) $trip->getId());

        try {
            $tripReportMailerService->sendTripReport($email, $report, $pdf, $filename, $sender, $customMessage);
            $this->addFlash('success', sprintf('Trip report sent to %s successfully.', $email));
        } catch (\RuntimeException $e) {
            if (str_contains(strtolower($e->getMessage()), 'null://null')) {
                $this->addFlash('danger', 'Email is not configured for real delivery yet (MAILER_DSN is null://null). Configure SMTP to receive messages.');
            } else {
                $this->addFlash('danger', 'Unable to send the email right now. Please verify mailer settings and try again.');
            }
        } catch (\Throwable) {
            $this->addFlash('danger', 'Unable to send the email right now. Please verify mailer settings and try again.');
        }

        return $this->redirectBack($request, $trip);
    }

    #[Route('/trips/{id}/qr.png', name: 'trip_qr_png', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/admin/trips/{id}/qr.png', name: 'admin_trip_qr_png', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function qrPng(Request $request, Trip $trip, TripQrCodeService $tripQrCodeService): Response
    {
        $this->enforceRoleByRouteName((string) $request->attributes->get('_route'));

        $tripUrl = $this->generateUrl('trip_show', ['id' => $trip->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $qrImage = $tripQrCodeService->buildTripQrImage($trip, $tripUrl);

        return new Response($qrImage['content'], 200, [
            'Content-Type' => $qrImage['mimeType'],
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function redirectBack(Request $request, Trip $trip): RedirectResponse
    {
        $route = str_starts_with((string) $request->attributes->get('_route', ''), 'admin_')
            ? 'admin_trip_show'
            : 'trip_show';
        $currency = trim((string) $request->request->get('currency', ''));
        $params = ['id' => $trip->getId()];
        if ($currency !== '') {
            $params['currency'] = $currency;
        }

        return $this->redirectToRoute($route, $params);
    }

    private function enforceRoleByRouteName(string $routeName): void
    {
        if (str_starts_with($routeName, 'admin_')) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            return;
        }

        $this->denyAccessUnlessGranted('ROLE_USER');
    }
}
