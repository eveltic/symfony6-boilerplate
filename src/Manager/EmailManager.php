<?php


namespace App\Manager;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class Email
 * @package App\Manager
 */
class EmailManager
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * Email constructor.
     * @param MailerInterface $mailer
     * @param TranslatorInterface $translator
     */
    public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * @param string $sTo
     * @param string $sSubject
     * @param string $sTemplate
     * @param array $aContext
     * @return TemplatedEmail
     * @throws TransportExceptionInterface
     */
    public function create(string $sTo, string $sSubject, string $sTemplate, array $aContext = []): TemplatedEmail
    {
        /* Check to email */
        if (!filter_var($sTo, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('The provided email is not valid');
        }

        /* Send email to account owner */
        $oEmail = (new TemplatedEmail())
            ->from(new Address($_SERVER['MAILER_FROM'], $_SERVER['MAILER_FROM_NAME']))
            ->to($sTo)
            ->subject(sprintf('[%s] %s', $_SERVER['APP_TITLE'], $this->translator->trans($sSubject)))
            ->htmlTemplate($sTemplate)
            ->context($aContext);

        $this->mailer->send($oEmail);
        return $oEmail;
    }
}