<?php

namespace App\Controller\Frontend;

use App\Constants\UserConstans;
use App\Constants\UserNoticeConstants;
use App\Entity\User;
use App\Form\Frontend\UserRegistrationFormType;
use App\Manager\EmailManager;
use App\Manager\UserNoticeManager;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[Route('/security', defaults: [], name: 'app_frontend_security_')]
class SecurityController extends AbstractController
{
    /*
     * TODO: login_link + user_checker + retrieve password symfonycasts + 
     * TODO: Meter custom authenticator:
     *  - https://symfony.com/doc/current/security/custom_authenticator.html
     *  - LoginformAuthenticator.php
     *  - security.yaml
     * 
     */
    #[Route('/', name: 'index')]
    public function index(Request $request, UserNoticeManager $userNoticeManager): Response
    {

        // Track action
        // $userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'Account verified', 'You have been verified successfully into the system');
        // $userNoticeManager->setNotice(
        //     UserNoticeConstants::TYPE_DEBUG,
        //     ['message' => 'Hello!', 'variables' => []],
        //     ['message' => 'hello message!!!!!', 'variables' => []]);
        return new Response('<!DOCTYPE html><html><head></head><body>Security index controller</body></html>');
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If the user is logged throw it out
        if ($this->getUser()) {return $this->redirectToRoute('app_frontend_security_register'); }
        // TODO: Track action
        // Show login form
        return $this->render('frontend/security/login.html.twig', ['last_username' => $authenticationUtils->getLastUsername(), 'error' => $authenticationUtils->getLastAuthenticationError()]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/verify', name: 'verify')]
    public function verify(UserNoticeManager $userNoticeManager, EmailVerifier $emailVerifier, EmailManager $emailManager, Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        // Get user
        $oUser = $userRepository->find($request->get('id', null));
        if (!$oUser instanceof UserInterface) {return $this->redirectToRoute('app_frontend_security_register'); }
        // Validate email confirmation link, sets User::isVerified=true and persists
        try {
            $emailVerifier->handleEmailConfirmation($request, $oUser);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_frontend_security_register');
        }
        // Add flash and send confirmation email
        $this->addFlash('success', sprintf('%s.', $translator->trans('Your email address has been verified')));
        $emailManager->create($oUser->getEmail(), 'Account verified', 'email/verify.html.twig', ['username' => $oUser->getEmail(),]);
        // Track action
        $userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'Account verified', 'You have been verified successfully into the system', $oUser);
        // Redirect out
        return $this->redirectToRoute('app_frontend_index_index');
    }

    #[Route('/register', name: 'register')]
    public function register(UserNoticeManager $userNoticeManager, VerifyEmailHelperInterface $helper, EmailManager $emailManager, TranslatorInterface $translator, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // If the user is logged throw it out
        if ($this->getUser()) { return $this->redirectToRoute('app_frontend_index_index'); }
        // Create new user
        $oUser = new User();
        // Create new form
        $oForm = $this->createForm(UserRegistrationFormType::class, $oUser);
        $oForm->handleRequest($request);
        // If the form has been submitted
        if ($oForm->isSubmitted() && $oForm->isValid()) {
            // Generate user details and persist the object
            $oUser->setPassword($userPasswordHasher->hashPassword($oUser, $oForm->get('plainPassword')->getData()));
            $oUser->setState(UserConstans::USER_STATUS_ACTIVE);
            $oUser->setUuid(Uuid::v4());
            $oUser->setCreatedAt(new \DateTimeImmutable('now'));
            $oUser->setRoles(['ROLE_USER']);
            $userRepository->add($oUser, true);
            // Generate signature components and send email with activation link
            $signatureComponents = $helper->generateSignature('app_frontend_security_verify', $oUser->getId(), $oUser->getEmail(), ['id' => $oUser->getId()]);
            $emailManager->create($oUser->getEmail(), 'Registration confirmation', 'email/register.html.twig', ['username' => $oUser->getEmail(), 'signedUrl' => $signatureComponents->getSignedUrl(), 'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(), 'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),]);
            // Add success flash
            $this->addFlash('success', sprintf('%s.', $translator->trans('You have registered successfully, please check your email to activate your account')));
            // Track action
            $userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'Registration completed', 'You have been registered successfully into the system', $oUser);
            // Redirect out
            return $this->redirectToRoute('app_frontend_index_index');
        }
        // Render template with the form
        return $this->render('frontend/security/register.html.twig', ['oForm' => $oForm->createView(), ]);
    }
}
