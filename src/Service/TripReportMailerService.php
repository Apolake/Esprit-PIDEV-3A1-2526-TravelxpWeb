<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class TripReportMailerService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        #[Autowire('%env(string:MAILER_DSN)%')]
        private readonly string $mailerDsn,
    ) {
    }

    /**
     * @param array<string, mixed> $report
     */
    public function sendTripReport(
        string $recipientEmail,
        array $report,
        string $pdfBinary,
        string $pdfFilename,
        ?User $sender = null,
        ?string $customMessage = null,
    ): void {
        if (str_starts_with(strtolower(trim($this->mailerDsn)), 'null://')) {
            throw new \RuntimeException('Mailer transport is configured as null://null and will not deliver emails.');
        }

        $fromAddress = new Address('no-reply@travelxp.local', 'TravelXP');

        $email = (new TemplatedEmail())
            ->from($fromAddress)
            ->to(new Address(trim($recipientEmail)))
            ->subject(sprintf('Trip Report: %s', (string) ($report['trip']?->getTripName() ?? 'Trip')))
            ->htmlTemplate('emails/trip_report.html.twig')
            ->context([
                'report' => $report,
                'sender' => $sender,
                'customMessage' => trim((string) ($customMessage ?? '')),
            ])
            ->attach($pdfBinary, $pdfFilename, 'application/pdf');

        $this->mailer->send($email);
    }
}
