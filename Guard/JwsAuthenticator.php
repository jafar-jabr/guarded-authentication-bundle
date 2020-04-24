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

use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse\ApiProblem;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse\ApiResponseFactory;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSEncoder\JWSEncoderInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSExtractor\TokenExtractor;
use Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class JwsAuthenticator.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class JwsAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var JWSEncoderInterface
     */
    private $jwtEncoder;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ApiResponseFactory
     */
    private $responseFactory;

    /**
     * @var string
     */
    private $loginRoute;

    /**
     * JwsAuthenticator constructor.
     *
     * @param JWSEncoderInterface $jwtEncoder
     * @param RouterInterface     $router
     * @param ApiResponseFactory  $responseFactory
     * @param string              $loginRoute
     */
    public function __construct(
        JWSEncoderInterface $jwtEncoder,
        RouterInterface $router,
        ApiResponseFactory $responseFactory,
        string $loginRoute
    ) {
        $this->jwtEncoder      = $jwtEncoder;
        $this->router          = $router;
        $this->responseFactory = $responseFactory;
        $this->loginRoute      = $loginRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        $loginRoute    = $this->loginRoute;
        $isLoginSubmit = $request->attributes->get('_route') == $loginRoute && $request->isMethod('POST');
        if ($isLoginSubmit) {
            return null;
        }
        $extractor = new TokenExtractor('Bearer', 'Authorization');

        return $extractor->extract($request) ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
        } catch (ApiException $e) {
            throw new CustomUserMessageAuthenticationException($e->getMessage());
        }

        $username = $data['username'];

        return $this->loadUser($userProvider, $username);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    protected function getLoginUrl()
    {
        $loginRoute = $this->loginRoute;

        return $this->router->generate($loginRoute);
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
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $apiProblem = new ApiProblem(401);
        $apiProblem->set('detail', $exception->getMessageKey());

        return $this->responseFactory->createResponse($apiProblem);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $apiProblem = new ApiProblem(401);
        $message    = $authException ? $authException->getMessageKey() : 'Invalid credentials';
        $apiProblem->set('detail', $message);

        return $this->responseFactory->createResponse($apiProblem);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return (bool) $this->getCredentials($request);
    }

    /**
     * @param UserProviderInterface $userProvider
     * @param string                $username
     *
     * @return UserInterface
     */
    private function loadUser(UserProviderInterface $userProvider, string $username)
    {
        try {
            return $userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e) {
            throw new CustomUserMessageAuthenticationException($e->getMessage());
        }
    }
}
