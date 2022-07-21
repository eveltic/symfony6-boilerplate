<?php

namespace App\Security;

use App\Constants\UserNoticeConstants;
use App\Manager\UserNoticeManager;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    /**
     * Frontend user login route
     */
    public const FRONTEND_LOGIN_ROUTE = 'app_frontend_security_login';
    /**
     * Frontend homepage
     */
    public const FRONTEND_INDEX_ROUTE = 'app_frontend_index_index';
    /**
     * Backend homepage
     * TODO: Change route
     */
    public const BACKEND_INDEX_ROUTE = 'app_frontend_index_index';
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;
    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var UserNoticeManager
     */
    private UserNoticeManager $userNoticeManager;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param Security $security
     * @param UserSecurityNoticeManager $userSecurityNoticeManager
     * @param UserRepository $userRepository
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security, UserNoticeManager $userNoticeManager, UserRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->userNoticeManager = $userNoticeManager;
        $this->userRepository = $userRepository;
    }

    /**
     * Does the authenticator support the given Request?
     *
     * If this returns false, the authenticator will be skipped.
     *
     * Returning null means authenticate() can be called lazily when accessing the token storage.
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return self::FRONTEND_LOGIN_ROUTE === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    /**
     * Return the URL to the login page.
     * @param Request $request
     * @return string
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::FRONTEND_LOGIN_ROUTE);
    }

    /**
     * Method used to check the user credentials and return a passport.
     *
     * @param Request $request
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        $sEmail = $request->request->get('_username', '');
        $request->getSession()->set(Security::LAST_USERNAME, $sEmail);
        return new Passport(new UserBadge($sEmail), new PasswordCredentials($request->request->get('_password', '')), [new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),]);
    }

    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        $this->userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'User logged in successfully', 'You have been logged in succesfully', $this->security->getUser());
        $sRoute = $this->security->isGranted('ROLE_ADMIN') ? self::BACKEND_INDEX_ROUTE : self::FRONTEND_INDEX_ROUTE;
        return new RedirectResponse($this->urlGenerator->generate($sRoute));
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $oUser = $this->userRepository->findOneBy(['email' => $request->get('email')]);
        if($oUser instanceof UserInterface){
            $this->userNoticeManager->setNotice(UserNoticeConstants::TYPE_SECURITY, 'User login error', sprintf('User login error: %s', $exception->getMessage()), $this->security->getUser());
        }
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return new RedirectResponse($this->getLoginUrl($request));
    }
}
