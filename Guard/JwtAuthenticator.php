<?php
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/4/2017
 * Time: 4:15 PM
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
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Extractor\TokenExtractor;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiException;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Encoder\JWTEncoderInterface;

class JwtAuthenticator extends AbstractGuardAuthenticator
{
    private $jwtEncoder;
    private $router;
    private $responseFactory;
    private $loginRoute;
    private $homeRoute;

    public function __construct(
        JWTEncoderInterface $jwtEncoder,
        RouterInterface $router,
        ApiResponseFactory $responseFactory,
        $loginRoute,
        $homeRoute
    )
    {
        $this->jwtEncoder = $jwtEncoder;
        //$this->em = $em;
        $this->router = $router;
        $this->responseFactory = $responseFactory;
        $this->loginRoute = $loginRoute;
        $this->homeRoute = $homeRoute;
    }

    public function getCredentials(Request $request)
    {
        $extractor = new TokenExtractor(
            'Bearer',
            'Authorization'
        );
        $token = $extractor->extract($request);
        if (!$token) {
            return;
        }
        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
            $username = $data['username'];
            $user = $userProvider->loadUserByUsername($username);
            if ($user) {
                return $user;
            } else {
                throw new CustomUserMessageAuthenticationException('Invalid Email');
            }
        } catch (ApiException $e) {
            throw new CustomUserMessageAuthenticationException($e->getMessage());
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    protected function getLoginUrl()
    {
        $loginRoute = $this->loginRoute;
        return $this->router->generate($loginRoute);
    }

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

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $apiProblem = new ApiProblem(401);
        $message = $authException ? $authException->getMessageKey() : 'Invalid credentials';
        $apiProblem->set('detail', $message);
        return $this->responseFactory->createResponse($apiProblem);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        $homeRoute = $this->homeRoute;
        return $this->router->generate($homeRoute);
    }
}
