<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests\Api\JWSEncoder;

use Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiExceptionTest;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSProvider\JWSProviderInterfaceTest;
use PHPUnit\Framework\TestCase;


/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class JWSEncoder
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSEncoder
 */
class JWSEncoderTestTest implements JWSEncoderInterfaceTest
{
    /**
     * @var JWSProviderInterfaceTest
     */
    protected $jwsProvider;

    /**
     * @param JWSProviderInterfaceTest $jwsProvider
     */
    public function __construct(JWSProviderInterfaceTest $jwsProvider)
    {
        $this->jwsProvider = $jwsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function encode(array $payload)
    {
        try {
            $jws = $this->jwsProvider->create($payload);
        } catch (\InvalidArgumentException $e) {
            throw new ApiExceptionTest(
                ApiExceptionTest::INVALID_CONFIG,
                'An error occurred while trying 
                to encode the JWT token. Please verify your configuration (private key/passPhrase)',
                $e
            );
        }
        if (!$jws->isSigned()) {
            throw new ApiExceptionTest(
                ApiExceptionTest::UNSIGNED_TOKEN,
                'Unable to create a signed JWT from the given configuration.'
            );
        }

        return $jws->getToken();
    }

    /**
     * {@inheritdoc}
     */
    public function decode($token)
    {
        try {
            $jws = $this->jwsProvider->load($token);
        } catch (\Exception $e) {
            throw new ApiExceptionTest(ApiExceptionTest::INVALID_TOKEN, 'Invalid JWT Token', $e);
        }
        if ($jws->isInvalid()) {
            throw new ApiExceptionTest(ApiExceptionTest::INVALID_TOKEN, 'Invalid JWT Token');
        }
        if ($jws->isExpired()) {
            throw new ApiExceptionTest(ApiExceptionTest::EXPIRED_TOKEN, 'Expired JWT Token');
        }
        if (!$jws->isVerified()) {
            throw new ApiExceptionTest(
                ApiExceptionTest::UNVERIFIED_TOKEN,
                'Unable to verify the given JWT through the given configuration.
                 If the encryption keys have been changed since your last authentication, please renew the token.
                 If the problem persists, verify that the configured passPhrase is valid.'
            );
        }

        return $jws->getPayload();
    }
}
