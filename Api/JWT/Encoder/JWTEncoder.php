<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Encoder;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiException;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Provider\JWSProviderInterface;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */

class JWTEncoder implements JWTEncoderInterface
{
    /**
     * @var JWSProviderInterface
     */
    protected $jwsProvider;

    /**
     * @param JWSProviderInterface $jwsProvider
     */
    public function __construct(JWSProviderInterface $jwsProvider)
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
            throw new ApiException(ApiException::INVALID_CONFIG,
                'An error occurred while trying 
                to encode the JWT token. Please verify your configuration (private key/passphrase)', $e);
        }
        if (!$jws->isSigned()) {
            throw new ApiException(ApiException::UNSIGNED_TOKEN,
                'Unable to create a signed JWT from the given configuration.');
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
            throw new ApiException(ApiException::INVALID_TOKEN, 'Invalid JWT Token', $e);
        }
        if ($jws->isInvalid()) {
            throw new ApiException(ApiException::INVALID_TOKEN, 'Invalid JWT Token');
        }
        if ($jws->isExpired()) {
            throw new ApiException(ApiException::EXPIRED_TOKEN, 'Expired JWT Token');
        }
        if (!$jws->isVerified()) {
            throw new ApiException(ApiException::UNVERIFIED_TOKEN,
                'Unable to verify the given JWT through the given configuration.
                 If the encryption options have been changed since your last authentication, please renew the token.
                 If the problem persists, verify that the configured passphrase are valid.');
        }
        return $jws->getPayload();
    }
}
