<?php


namespace App\Security;

use App\Constants\UserConstans;
use App\Constants\UserNoticeConstants;
use App\Entity\User;
use App\Manager\UserNoticeManager;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    private UserNoticeManager $userNoticeManager;

    public function __construct(UserNoticeManager $userNoticeManager)
    {
        $this->userNoticeManager = $userNoticeManager;
    }

    public function checkPreAuth(UserInterface $oUser)
    {
        if (!$oUser instanceof User) {
            return;
        }

        /* Check user status */
        if($oUser->getState() != UserConstans::USER_STATUS_ACTIVE)
        {
            $this->userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'Inactive user login attempt', sprintf('%s login attempt', UserConstans::getConstant($oUser->getState())), $oUser);
            throw new CustomUserMessageAuthenticationException(sprintf('Your user state is %s', UserConstans::getConstant($oUser->getState())));
        }
    }

    public function checkPostAuth(UserInterface $oUser)
    {
        if (!$oUser instanceof User) {
            return;
        }
    }
}