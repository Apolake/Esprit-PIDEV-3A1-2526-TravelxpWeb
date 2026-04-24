<?php

namespace App\Service;

use App\Entity\User;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use OTPHP\TOTP;

class TotpManager
{
    public function __construct(
        private readonly RecoveryCodeManager $recoveryCodeManager,
        private readonly string $issuer = 'TravelXP',
    ) {
    }

    public function createSecret(): string
    {
        return TOTP::create()->getSecret();
    }

    public function verifyCode(User $user, string $code): bool
    {
        $secret = $user->getTotpSecret();
        if ($secret === null || '' === trim($secret)) {
            return false;
        }

        $normalized = preg_replace('/\s+/', '', trim($code)) ?? '';
        if (preg_match('/^\d{6}$/', $normalized) !== 1) {
            return false;
        }

        return $this->createTotp($secret, $user)->verify($normalized, null, 29);
    }

    public function getProvisioningUri(User $user): string
    {
        $secret = $user->getTotpSecret();
        if ($secret === null || '' === trim($secret)) {
            throw new \RuntimeException('TOTP secret is missing.');
        }

        return $this->createTotp($secret, $user)->getProvisioningUri();
    }

    public function getQrCodeDataUri(User $user): string
    {
        $qrCode = new QrCode(
            data: $this->getProvisioningUri($user),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 260,
            margin: 12,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        return (new PngWriter())->write($qrCode)->getDataUri();
    }

    /**
     * @return list<string>
     */
    public function generateRecoveryCodesForUser(User $user): array
    {
        $codes = $this->recoveryCodeManager->generateCodes(8);
        $user->setTotpRecoveryCodes($this->recoveryCodeManager->hashCodes($codes));

        return $codes;
    }

    public function consumeRecoveryCodeForUser(User $user, string $code): bool
    {
        $remaining = $this->recoveryCodeManager->consumeCode($code, $user->getTotpRecoveryCodes());
        if ($remaining === null) {
            return false;
        }

        $user->setTotpRecoveryCodes($remaining);

        return true;
    }

    private function createTotp(string $secret, User $user): TOTP
    {
        $totp = TOTP::create($secret);
        $totp->setLabel($user->getEmail() ?? 'user');
        $totp->setIssuer($this->issuer);
        $totp->setDigits(6);
        $totp->setPeriod(30);

        return $totp;
    }
}
