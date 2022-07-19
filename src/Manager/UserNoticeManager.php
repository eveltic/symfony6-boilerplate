<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\UserNotice;
use App\Manager\UserAgentManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserNoticeManager
{
    const TYPE_SECURITY = 1;
    const TYPE_INFO = 2;
    const TYPE_DEBUG = 3;
    const TYPE_ERROR = 4;

    /**
     * @param Security $security
     * @param UserAgentManager $userAgentManager
     * @param EntityManagerInterface $entityManager
     * @param RequestStack $requestStack
     * @param TranslatorInterface $translator
     */
    public function __construct(Security $security, UserAgentManager $userAgentManager, EntityManagerInterface $entityManager, RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->userAgentManager = $userAgentManager;
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * @param User|UserInterface|null $oUser
     * @return ?UserInterface
     */
    private function getUser(null|User|UserInterface $oUser = null): ?UserInterface
    {
        return (!$oUser instanceof User or !$oUser instanceof UserInterface) ? $this->security->getUser() : $oUser;
    }

    /**
     * @param string|null $sFingerprint
     * @return string
     */
    private function getFingerPrint(?string $sFingerprint = null): string
    {
        return (!empty($sFingerprint)) ? $sFingerprint : $this->userAgentManager->getFingerprint();
    }

    /**
     * @param DateTimeImmutable|null $oDatetime
     * @return DateTimeImmutable
     */
    private function getDatetime(?\DateTimeImmutable $oDatetime = null): \DateTimeImmutable
    {
        return ($oDatetime instanceof \DateTimeImmutable) ? $oDatetime : new DateTimeImmutable('now');
    }

    /**
     * @param Request|null $oRequest
     * @return Request
     */
    private function getRequest(?Request $oRequest = null): Request
    {
        return (!$oRequest instanceof Request) ? $this->requestStack->getMainRequest() : $oRequest;
    }

    /**
     * @param string|array $message
     * @return array
     * @throws InvalidArgumentException
     */
    public function translateNotice(string|array $message): array
    {
        if(is_array($message)){
            if(!isset($message['message'])){
                throw new InvalidArgumentException('The setMessage method needs an array with a "message" key that contains the translatable message');
            }

            if(isset($message['variables'])){
                if(!is_array($message['variables'])){
                    throw new InvalidArgumentException('The setMessage method needs an array with a "variables" key that contains the array with the translatable variables');
                }
            } else {
                $message['variables'] = [];
            }
        } else if (is_string($message)) {
            $tempMessage = [];
            $tempMessage['message'] = $message;
            $tempMessage['variables'] = [];
            $message = $tempMessage;
        } else {
            throw new InvalidArgumentException('The setMessage method needs a parameter set as array with the keys message and variables or a simple string with the message');
        }

        return $message;
    }

    public function setNotice(
        int $iType,
        array|string $mMessage
        , array|string $mSubject
        , ?DateTimeImmutable $oDatetime = null
        , ?Request $oRequest = null
        , ?UserInterface $oUser = null
        ): UserNotice
    {
        if(!in_array($iType, [1,2,3,4])) throw new InvalidArgumentException('Notification type must be UserNoticeManager::TYPE_SECURITY, UserNoticeManager::TYPE_INFO, UserNoticeManager::TYPE_DEBUG or UserNoticeManager::TYPE_ERROR');
        $oDatetime = $this->getDatetime($oDatetime);
        $oRequest = $this->getRequest($oRequest);
        $oUser = $this->getUser($oUser);

        $oUserNotice = new UserNotice();
        $oUserNotice->setCreatedAt($this->getDatetime($oDatetime));
        $oUserNotice->setFingerprint($this->getFingerPrint());
        $oUserNotice->setIp($oRequest->getClientIp());
        $oUserNotice->setMessage($this->translateNotice($mMessage));
        $oUserNotice->setMetadata($this->userAgentManager->getBrowserMetadata());
        $oUserNotice->setReaded(false);
        $oUserNotice->setSubject($this->translateNotice($mSubject));
        $oUserNotice->setType($iType);
        $oUserNotice->setUser($this->getUser());

        $this->entityManager->persist($oUserNotice);
        $this->entityManager->flush();
        return $oUserNotice;
    }
}
