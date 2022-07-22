<?php

namespace App\EventSubscriber;

use App\Constants\UserNoticeConstants;
use App\Manager\UserNoticeManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    private UserNoticeManager $userNoticeManager;

    public function __construct(UserNoticeManager $userNoticeManager)
    {
        $this->userNoticeManager = $userNoticeManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }

    public function onLogoutEvent($event): void
    {
        if(null !== $event->getToken() AND $event->getToken()->getUser() instanceof UserInterface){

            $this->userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'User logout successfully', 'You have been logged out successfully', $event->getToken()->getUser());
        }
    }
}
