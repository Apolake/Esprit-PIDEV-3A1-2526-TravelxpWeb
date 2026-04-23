<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TotpManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TwoFactorController extends AbstractController
{
    #[Route('/2fa/challenge', name: 'app_2fa_challenge', methods: ['GET', 'POST'])]
    public function challenge(Request $request, TotpManager $totpManager, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getCurrentUser();
        if (!$user->isTotpEnabled()) {
            return $this->redirectToRoute('app_home');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('totp_challenge', (string) $request->request->get('_token'))) {
                $this->addFlash('error', 'Invalid CSRF token.');

                return $this->redirectToRoute('app_2fa_challenge');
            }

            $code = (string) $request->request->get('code', '');

            $validTotp = $totpManager->verifyCode($user, $code);
            $validRecovery = false;
            if (!$validTotp) {
                $validRecovery = $totpManager->consumeRecoveryCodeForUser($user, $code);
                if ($validRecovery) {
                    $entityManager->flush();
                }
            }

            if ($validTotp || $validRecovery) {
                $session = $request->getSession();
                $session->set('totp_verified_user_id', $user->getId());
                $targetPath = (string) $session->get('totp_target_path', '');
                $session->remove('totp_target_path');

                if ($validRecovery) {
                    $this->addFlash('warning', 'Recovery code used. Generate a new set in your 2FA settings soon.');
                }

                if ($targetPath !== '' && str_starts_with($targetPath, '/')) {
                    return $this->redirect($targetPath);
                }

                return $this->redirectToRoute('app_home');
            }

            $this->addFlash('error', 'Invalid authentication code.');
        }

        return $this->render('security/two_factor_challenge.html.twig');
    }

    #[Route('/profile/2fa/setup', name: 'app_profile_2fa_setup', methods: ['GET', 'POST'])]
    public function setup(Request $request, TotpManager $totpManager, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getCurrentUser();
        $session = $request->getSession();

        if ($user->getTotpSecret() === null || '' === trim((string) $user->getTotpSecret())) {
            $user->setTotpSecret($totpManager->createSecret());
            $codes = $totpManager->generateRecoveryCodesForUser($user);
            $session->set('totp_setup_recovery_codes', $codes);
            $entityManager->flush();
        }

        if ($request->isMethod('POST')) {
            $action = (string) $request->request->get('action', 'enable');

            if ($action === 'regenerate_recovery') {
                if (!$this->isCsrfTokenValid('regenerate_totp_recovery', (string) $request->request->get('_token'))) {
                    $this->addFlash('error', 'Invalid CSRF token.');

                    return $this->redirectToRoute('app_profile_2fa_setup');
                }

                $codes = $totpManager->generateRecoveryCodesForUser($user);
                $session->set('totp_setup_recovery_codes', $codes);
                $entityManager->flush();
                $this->addFlash('success', 'Recovery codes regenerated.');

                return $this->redirectToRoute('app_profile_2fa_setup');
            }

            if (!$this->isCsrfTokenValid('enable_totp', (string) $request->request->get('_token'))) {
                $this->addFlash('error', 'Invalid CSRF token.');

                return $this->redirectToRoute('app_profile_2fa_setup');
            }

            $code = (string) $request->request->get('code', '');
            if (!$totpManager->verifyCode($user, $code)) {
                $this->addFlash('error', 'Invalid authenticator code.');

                return $this->redirectToRoute('app_profile_2fa_setup');
            }

            $user->setTotpEnabled(true);
            $entityManager->flush();
            $session->set('totp_verified_user_id', $user->getId());
            $session->remove('totp_setup_recovery_codes');
            $this->addFlash('success', 'Two-factor authentication enabled.');

            return $this->redirectToRoute('app_profile_show');
        }

        $recoveryCodes = $session->get('totp_setup_recovery_codes', []);

        return $this->render('profile/two_factor_setup.html.twig', [
            'qrCodeDataUri' => $totpManager->getQrCodeDataUri($user),
            'secret' => $user->getTotpSecret(),
            'recoveryCodes' => is_array($recoveryCodes) ? $recoveryCodes : [],
        ]);
    }

    #[Route('/profile/2fa/disable', name: 'app_profile_2fa_disable', methods: ['POST'])]
    public function disable(Request $request, TotpManager $totpManager, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getCurrentUser();
        if (!$user->isTotpEnabled()) {
            return $this->redirectToRoute('app_profile_show');
        }

        if (!$this->isCsrfTokenValid('disable_totp', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');

            return $this->redirectToRoute('app_profile_show');
        }

        $code = (string) $request->request->get('code', '');
        $validTotp = $totpManager->verifyCode($user, $code);
        $validRecovery = false;
        if (!$validTotp) {
            $validRecovery = $totpManager->consumeRecoveryCodeForUser($user, $code);
        }

        if (!$validTotp && !$validRecovery) {
            $this->addFlash('error', 'Invalid verification code.');

            return $this->redirectToRoute('app_profile_show');
        }

        $user->setTotpEnabled(false);
        $user->setTotpSecret(null);
        $user->setTotpRecoveryCodes([]);
        $entityManager->flush();

        $request->getSession()->remove('totp_verified_user_id');
        $request->getSession()->remove('totp_setup_recovery_codes');
        $this->addFlash('success', 'Two-factor authentication disabled.');

        return $this->redirectToRoute('app_profile_show');
    }

    private function getCurrentUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not authenticated.');
        }

        return $user;
    }
}
