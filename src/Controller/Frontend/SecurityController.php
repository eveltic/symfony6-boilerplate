<?php

namespace App\Controller\Frontend;

use App\Constants\UserConstants;
use App\Constants\UserNoticeConstants;
use App\Entity\User;
use App\Form\Frontend\UserLoginLinkFormType;
use App\Form\Frontend\UserRegistrationFormType;
use App\Form\Frontend\UserRequestFormType;
use App\Form\Frontend\UserResetFormType;
use App\Manager\EmailManager;
use App\Manager\UserNoticeManager;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[Route('/security', defaults: [], name: 'app_frontend_security_')]
class SecurityController extends AbstractController
{
    use ResetPasswordControllerTrait;

    #[Route('/', name: 'index')]
    public function index(Request $request, UserNoticeManager $userNoticeManager): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_frontend_index_index'); 
        }

        // Track action
        // $userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'Account verified', 'You have been verified successfully into the system');
        // $userNoticeManager->setNotice(
        //     UserNoticeConstants::TYPE_DEBUG,
        //     ['message' => 'Hello!', 'variables' => []],
        //     ['message' => 'hello message!!!!!', 'variables' => []]);
        return new Response('<!DOCTYPE html><html><head></head><body>Security index controller</body></html>');
    }

    #[Route('/login/link', name: 'login_link')]
    public function loginLink(UserNoticeManager $userNoticeManager, EmailManager $emailManager, LoginLinkHandlerInterface $loginLinkHandler, Request $request, UserRepository $userRepository): Response
    {
        // If the user is logged throw it out
        if ($this->getUser()) {return $this->redirectToRoute('app_frontend_index_index'); }

        $oForm = $this->createForm(UserLoginLinkFormType::class, []);
        $oForm->handleRequest($request);
        if ($oForm->isSubmitted() && $oForm->isValid()) {
            $oUser = $userRepository->findOneBy(['email' => $oForm->get('email')->getData()]);
            $loginLinkDetails = $loginLinkHandler->createLoginLink($oUser);
            // Send email
            $emailManager->create($oUser->getEmail(), 'Login link requested', 'email/login_link.html.twig', ['username' => $oUser->getEmail(), 'link' => $loginLinkDetails->getUrl(),]);
            // Track action
            $userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'User Login link requested', 'A new login link has been requested', $oUser);
            return $this->redirectToRoute('app_frontend_security_login', []);
        }
        return $this->render('app/frontend/security/login_link.html.twig', ['oForm' => $oForm->createView(),]);
    }

    #[Route('/login/check', name: 'login_link_check')]
    public function loginCheck(Request $request): Response
    {
        // If the user is logged throw it out
        if ($this->getUser()) { return $this->redirectToRoute('app_frontend_index_index'); }
        // If the required data isn't present redirect out
        if(empty($request->query->get('hash')) or empty($request->query->get('expires')) or empty($request->query->get('user'))){ return $this->redirectToRoute('app_frontend_security_login_link'); }
        // Render a template with the button
        return $this->render('app/frontend/security/login_check.html.twig', ['expires' => $request->query->get('expires'),'user' => $request->query->get('user'),'hash' => $request->query->get('hash'),]);
    }

    #[Route('/request', name: 'request')]
    public function request(UserNoticeManager $userNoticeManager, Request $request, UserRepository $userRepository, ResetPasswordHelperInterface $resetPasswordHelper, EmailManager $emailManager): Response
    {
        // If the user is logged throw it out
        if ($this->getUser()) {return $this->redirectToRoute('app_frontend_index_index'); }

        $oForm = $this->createForm(UserRequestFormType::class);
        $oForm->handleRequest($request);

        if ($oForm->isSubmitted() && $oForm->isValid()) {
            $oUser = $userRepository->findOneBy(['email' => $oForm->get('email')->getData()]);
            if (!$oUser instanceof UserInterface) { return $this->redirectToRoute('app_frontend_security_check'); }
            
            // Get reset token
            try {
                $resetToken = $resetPasswordHelper->generateResetToken($oUser);
            } catch (ResetPasswordExceptionInterface $e) {
                return $this->redirectToRoute('app_frontend_security_check');
            }

            // Send email
            $emailManager->create($oUser->getEmail(), 'Your password reset request', 'email/request.html.twig', ['username' => $oUser->getEmail(), 'resetToken' => $resetToken,]);
            // Track action
            $userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'User password reset requested', 'A password reset has been requested', $oUser);

            // Store the token object in session for retrieval in check-email route.
            $this->setTokenObjectInSession($resetToken);
            
            return $this->redirectToRoute('app_frontend_security_check');
        }

        return $this->render('app/frontend/security/request.html.twig', ['oForm' => $oForm->createView(),]);
    }

    #[Route('/reset/{token}', name: 'reset')]
    public function reset(EmailManager $emailManager, ResetPasswordHelperInterface $resetPasswordHelper, UserNoticeManager $userNoticeManager, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, string $token = null): Response
    {
        if ($this->getUser()) {return $this->redirectToRoute('app_frontend_index_index'); }

        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_frontend_security_reset');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) { throw $this->createNotFoundException('No reset password token found in the URL or in the session.'); }
        
        try {
            $oUser = $resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('error', sprintf('There was a problem validating your reset request - %s', $e->getReason()));
            return $this->redirectToRoute('app_frontend_security_request');
        }

        // The token is valid; allow the user to change their password.
        $oForm = $this->createForm(UserResetFormType::class);
        $oForm->handleRequest($request);

        if ($oForm->isSubmitted() && $oForm->isValid()) {
            // A password reset token should be used only once, remove it.
            $resetPasswordHelper->removeResetRequest($token);
            
            $oUser->setPassword($userPasswordHasher->hashPassword($oUser, $oForm->get('plainPassword')->getData()));
            $entityManager->flush();

            $this->cleanSessionAfterReset();

            // Send email
            $emailManager->create($oUser->getEmail(), 'Your password has been restored', 'email/reset.html.twig', ['username' => $oUser->getEmail(), 'password' => $oForm->get('plainPassword')->getData(),]);
            // Track action
            $userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'User password has been restored', 'A new password has been set', $oUser);

            return $this->redirectToRoute('app_frontend_index_index');
        }

        return $this->render('app/frontend/security/reset.html.twig', ['oForm' => $oForm->createView(),]);
    }

    #[Route('/check', name: 'check')]
    public function check(ResetPasswordHelperInterface $resetPasswordHelper): Response
    {
        if ($this->getUser()) {return $this->redirectToRoute('app_frontend_index_index'); }

        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('app/frontend/security/check.html.twig', ['resetToken' => $resetToken,]);
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If the user is logged throw it out
        if ($this->getUser()) {return $this->redirectToRoute('app_frontend_index_index'); }
        // Show login form
        return $this->render('app/frontend/security/login.html.twig', ['last_username' => $authenticationUtils->getLastUsername(), 'error' => $authenticationUtils->getLastAuthenticationError()]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/verify', name: 'verify')]
    public function verify(UserNoticeManager $userNoticeManager, EmailVerifier $emailVerifier, EmailManager $emailManager, Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        // If the user is logged throw it out
        if ($this->getUser()) { return $this->redirectToRoute('app_frontend_index_index'); }
        // Get user
        $oUser = $userRepository->findOneBy(['id' => $request->get('id', null)]);
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
            $oUser->setState(UserConstants::USER_STATUS_ACTIVE);
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
        return $this->render('app/frontend/security/register.html.twig', ['oForm' => $oForm->createView(), ]);
    }

    #[Route('/login/2fa/resend', name: '2fa_login_resend')]
    public function login_2fa_resend(CodeGeneratorInterface $codeGenerator): Response
    {
        // If the user is logged out redirect to login page
        if (!$this->getUser()) { return $this->redirectToRoute('app_frontend_security_login'); }

        // Resend the code
        $codeGenerator->reSend($this->getUser());

        return new RedirectResponse($this->generateUrl('2fa_login'));
    }
}
