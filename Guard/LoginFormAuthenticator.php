<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Guard;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

/**
 * Class LoginFormAuthenticator.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class LoginFormAuthenticator extends AbstractAuthenticator
{
    /**
     * @var string
     */
    private string $loginRoute;

    private RouterInterface $router;

    /**
     * @param string $loginRoute
     * @param RouterInterface $router
     */
    public function __construct(
        string $loginRoute,
        RouterInterface $router,
    ) {
        $this->loginRoute = $loginRoute;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $redirectRoute = $request->request->get("_target_path");
        try {
           return new RedirectResponse($this->router->generate($redirectRoute));
        }catch (RouteNotFoundException){
           die("<br>We didn't find a target url to redirect to after login, please refer to </br>`https://github.com/jafar-jabr/guarded-authentication-bundle/blob/master/Resources/doc/index.md#usage` </br> for how to set up it</b>");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') == $this->loginRoute && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     * @return Passport
     * ref https://symfony.com/doc/current/security/custom_authenticator.html#passport-attributes
     */
    public function authenticate(Request $request): Passport
    {
        $data = $request->request->all();
        $username = $data["_username"];
        $password = $data["_password"];
        $rememberMe = $data["_remember_me"] ?? null;
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $username
        );
        if($rememberMe) {
            $passport = new Passport(new UserBadge($username), new PasswordCredentials($password), [new RememberMeBadge()]);
        } else {
            $passport = new Passport(new UserBadge($username), new PasswordCredentials($password));
        }
        $oauthScope = "main";
        $passport->setAttribute('scope', $oauthScope);
        return $passport;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(
            Security::AUTHENTICATION_ERROR,
            $exception
        );
        return null;
    }
}
