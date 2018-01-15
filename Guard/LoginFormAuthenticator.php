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

use Jafar\Bundle\GuardedAuthenticationBundle\Form\GuardedLoginForm;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * {@inheritdoc}
 *
 * Class LoginFormAuthenticator
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var string
     */
    private $loginRoute;

    /**
     * @var string
     */
    private $homeRoute;

    /**
     * @var string
     */
    private $wrongEmail = 'Incorrect Email Provided!';

    /**
     * @var string
     */
    private $wrongPassword = 'Incorrect Password Provided!';

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param string $loginRoute
     * @param string $homeRoute
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder,
        string $loginRoute,
        string $homeRoute
    )
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->loginRoute = $loginRoute;
        $this->homeRoute = $homeRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        $loginRoute = $this->loginRoute;
        $isLoginSubmit = $request->attributes->get('_route') == $loginRoute && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            return null;
        }
        $form = $this->formFactory->create(GuardedLoginForm::class);
        $form->handleRequest($request);
        $data = $form->getData();
        if ($request->getSession()) {
            $request->getSession()->set(
                Security::LAST_USERNAME,
                $data['_username']
            );
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['_username'];
        return $this->loadUser($userProvider, $username);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['_password'];
        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;
        }

        throw new CustomUserMessageAuthenticationException($this->wrongPassword);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $homeRoute = $this->homeRoute;
        $url = $this->router->generate($homeRoute);

        return new RedirectResponse($url);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return parent::onAuthenticationFailure($request, $exception);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return parent::start($request, $authException);
    }

    /**
     * {@inheritdoc}
     */
    protected function getLoginUrl()
    {
        $loginRoute = $this->loginRoute;

        return $this->router->generate($loginRoute);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return (bool)$this->getCredentials($request);
    }

    /**
     * @param UserProviderInterface $userProvider
     * @param string $username
     * @return UserInterface
     */
    private function loadUser(UserProviderInterface $userProvider, string $username)
    {
        try {
            $user = $userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e) {
            throw new CustomUserMessageAuthenticationException($e->getMessage());
        }
        return $user;
    }
}
