<?php

namespace App\EventSubscriber;

use App\Constants\UserNoticeConstants;
use App\Manager\UserNoticeManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;

class SchecbTwoFactorSubscriber implements EventSubscriberInterface
{
    private UserNoticeManager $userNoticeManager;

    public function __construct(UserNoticeManager $userNoticeManager)
    {
        $this->userNoticeManager = $userNoticeManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TwoFactorAuthenticationEvents::FORM => 'onFormEvent',
        ];
    }

    public function onFormEvent($event): void
    {
        $this->userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'Two factor authentication code sent', 'A new code for two factor authentication has been send to the user', $event->getToken()->getUser());
    }
}
