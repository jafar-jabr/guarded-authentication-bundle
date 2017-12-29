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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Date: 11/02/2017
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $formFactory;
    private $router;
    private $passwordEncoder;
    private $loginForm;
    private $loginRoute;
    private $homeRoute;
    private $wrongEmail = 'Incorrect Email Provided!';
    private $wrongPassword = 'Incorrect Password Provided!';

    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder,
        $loginForm,
        $loginRoute,
        $homeRoute
    )
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->loginForm = $loginForm;
        $this->loginRoute = $loginRoute;
        $this->homeRoute = $homeRoute;
    }

    public function getCredentials(Request $request)
    {
        $loginForm = $this->loginForm;
        $loginRoute = $this->loginRoute;
        $isLoginSubmit = $request->attributes->get('_route') == $loginRoute && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            return null;
        }
        $form = $this->formFactory->create($loginForm);
        $form->handleRequest($request);
        $data = $form->getData();
        if($request->getSession()){
            $request->getSession()->set(
                Security::LAST_USERNAME,
                $data['_username']
            );
        }
        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['_username'];
        $user = $userProvider->loadUserByUsername($username);
        if ($user) {
            return $user;
        }
        throw new CustomUserMessageAuthenticationException($this->wrongEmail);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['_password'];
        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;
        }
        throw new CustomUserMessageAuthenticationException($this->wrongPassword);
    }

    protected function getLoginUrl()
    {
        $loginRoute =  $this->loginRoute;
        return $this->router->generate($loginRoute);
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        $homeRoute = $this->homeRoute;
        return $this->router->generate($homeRoute);
    }

    public function supportsRememberMe()
    {
        return true;
    }
}
