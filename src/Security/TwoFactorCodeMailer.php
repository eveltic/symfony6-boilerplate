<?php

namespace App\Security;

use App\Manager\EmailManager;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;

class TwoFactorCodeMailer implements AuthCodeMailerInterface
{
    private $emailManager;

    public function __construct(EmailManager $emailManager)
    {
        $this->emailManager = $emailManager;
    }

    public function sendAuthCode(TwoFactorInterface $oUser): void
    {
        $sAuthCode = $oUser->getEmailAuthCode();

            // Send email
            $this->emailManager->create($oUser->getEmail(), 'Authentication Code', 'email/login_2fa.html.twig', ['username' => $oUser->getEmail(), 'authCode' => $sAuthCode,]);
            // Track action
            // $userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'User password has been restored', 'A new password has been set', $oUser);

        // Send email
    }
}
