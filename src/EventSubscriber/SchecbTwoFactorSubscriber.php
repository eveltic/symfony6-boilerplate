<?php

namespace App\EventSubscriber;

use App\Constants\UserNoticeConstants;
use App\Manager\UserNoticeManager;
use Scheb\TwoFactorBundle\Security\Authentication\Exception\InvalidTwoFactorCodeException;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class SchecbTwoFactorSubscriber implements EventSubscriberInterface
{
    private UserNoticeManager $userNoticeManager;
    private RateLimiterFactory $schebTwoFactorLimiter;

    public function __construct(UserNoticeManager $userNoticeManager, RateLimiterFactory $schebTwoFactorLimiter)
    {
        $this->userNoticeManager = $userNoticeManager;
        $this->schebTwoFactorLimiter = $schebTwoFactorLimiter;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TwoFactorAuthenticationEvents::FORM => 'onFormEvent',
            TwoFactorAuthenticationEvents::FAILURE => 'onAuthenticationFailure',
            TwoFactorAuthenticationEvents::ATTEMPT => 'onAuthenticationAttempt',
            TwoFactorAuthenticationEvents::SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationAttempt($event): void
    {
        $oLimiter = $this->schebTwoFactorLimiter->create($event->getRequest()->getClientIp());
        if (!$oLimiter->consume(1)->isAccepted()) {
            throw new InvalidTwoFactorCodeException();
        }
    }

    public function onAuthenticationFailure($event): void
    {
        $this->userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'Two factor authentication failure', 'The two factor code used is wrong', $event->getToken()->getUser());
    }

    public function onAuthenticationSuccess($event): void
    {
        $this->userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'Two factor authentication success', 'The two factor code used is accepted', $event->getToken()->getUser());
    }

    public function onFormEvent($event): void
    {
        $this->userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'Two factor authentication code sent', 'A new code for two factor authentication has been send to the user', $event->getToken()->getUser());
    }
}
