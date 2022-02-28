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

use Exception;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse\ApiProblem;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse\ApiResponseFactory;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSEncoder\JWSEncoderInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSExtractor\TokenExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

/**
 * Class JwsAuthenticator.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class JwsAuthenticator extends AbstractAuthenticator
{
    /**
     * @var JWSEncoderInterface
     */
    private JWSEncoderInterface $jwtEncoder;
    /**
     * @var ApiResponseFactory
     */
    private ApiResponseFactory $responseFactory;

    private UserProviderInterface $userProvider;

    /**
     * JwsAuthenticator constructor.
     *
     * @param JWSEncoderInterface $jwtEncoder
     * @param ApiResponseFactory  $responseFactory
     */
    public function __construct(
        JWSEncoderInterface $jwtEncoder,
        ApiResponseFactory $responseFactory,
        UserProviderInterface $userProvider,
    ) {
        $this->jwtEncoder      = $jwtEncoder;
        $this->responseFactory = $responseFactory;
        $this->userProvider      = $userProvider;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    private function getCredentials(Request $request): bool|string|null
    {
        $extractor = new TokenExtractor('Bearer', 'Authorization');
        return $extractor->extract($request) ?? null;
    }

    /**
     * @param Request $request
     * @return Passport
     * ref https://symfony.com/doc/current/security/custom_authenticator.html#how-to-write-a-custom-authenticator
     */
    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);
        try {
            $data = $this->jwtEncoder->decode($credentials);
            $username = $data['username'];
            $cred = new CustomCredentials(
              function ($credentials, UserInterface $user) {
                  $authUser = $this->loadUser($credentials);
                  return !empty($authUser);
              },
              $username
          );
          return new Passport(new UserBadge($username), $cred);
        } catch (Exception $exception) {
            throw new CustomUserMessageAuthenticationException($exception->getMessage());
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $apiProblem = new ApiProblem(401);
        $apiProblem->set('detail', $exception->getMessageKey());
        return $this->responseFactory->createResponse($apiProblem);
    }

    /**
     * @param string $username
     * @return null|UserInterface
     */
    private function loadUser(string $username): ?UserInterface
    {
        try {
            return $this->userProvider->loadUserByIdentifier($username);
        }catch (UserNotFoundException){
            return null;
        }
    }
}
