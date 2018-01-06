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

use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Routing\RouterInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiProblem;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSExtractor\TokenExtractor;
use Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSEncoder\JWSEncoderInterface;

/**
 * {@inheritdoc}
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class JwsAuthenticator
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
     * @var string
     */
    private $homeRoute;

    /**
     * JwsAuthenticator constructor.
     *
     * @param JWSEncoderInterface $jwtEncoder
     * @param RouterInterface     $router
     * @param ApiResponseFactory  $responseFactory
     * @param string              $loginRoute
     * @param string              $homeRoute
     */
    public function __construct(
        JWSEncoderInterface $jwtEncoder,
        RouterInterface $router,
        ApiResponseFactory $responseFactory,
        string $loginRoute,
        string $homeRoute
    ) {
        $this->jwtEncoder = $jwtEncoder;
        $this->router = $router;
        $this->responseFactory = $responseFactory;
        $this->loginRoute = $loginRoute;
        $this->homeRoute = $homeRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        $extractor = new TokenExtractor('Bearer', 'Authorization');
        $token = $extractor->extract($request);
        if (!$token) {
            return null;
        }

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
            $userName = $data['username'];
            $user = $userProvider->loadUserByUsername($userName);
            if ($user) {
                return $user;
            } else {
                throw new CustomUserMessageAuthenticationException('Invalid Email');
            }
        } catch (ApiException $e) {
            throw new CustomUserMessageAuthenticationException($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
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
    public function supportsRememberMe()
    {
        return false;
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
        $message = $authException ? $authException->getMessageKey() : 'Invalid credentials';
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

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSuccessRedirectUrl()
    {
        $homeRoute = $this->homeRoute;

        return $this->router->generate($homeRoute);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return (bool) $this->getCredentials($request);
    }
}
