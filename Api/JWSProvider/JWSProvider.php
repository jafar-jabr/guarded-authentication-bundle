<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSProvider;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSCreator\JWSCreator;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\JWS;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\KeyLoaderInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\LoadedJWS;

/**
 * Class JWSProvider.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class JWSProvider implements JWSProviderInterface
{
    const SIGNATUREALGORITHM = 'RS256';

    /**
     * @var KeyLoaderInterface
     */
    private $keyLoader;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @var int
     */
    private $refresh_ttl;

    /**
     * @param KeyLoaderInterface $keyLoader
     * @param int                $ttl
     * @param int                $refresh_ttl
     *
     * @throws \InvalidArgumentException If the given ttl is not numeric
     */
    public function __construct(KeyLoaderInterface $keyLoader, $ttl, $refresh_ttl)
    {
        $this->keyLoader   = $keyLoader;
        $this->ttl         = $ttl;
        $this->refresh_ttl = $refresh_ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $payload, string $type = 'Main')
    {
        $jws    = new JWS(['alg' => self::SIGNATUREALGORITHM]);
        $claims = ['iat' => time()];
        if ('Main' == $type) {
            $claims['exp'] = time() + $this->ttl;
        } else {
            $claims['exp'] = time() + $this->refresh_ttl;
        }
        $jws->setPayload($payload + $claims);
        $jws->sign(
            $this->keyLoader->loadKey('private')
        );

        return new JWSCreator($jws->getTokenString(), $jws->isSigned());
    }

    /**
     * {@inheritdoc}
     */
    public function load($token)
    {
        $jws = JWS::load($token, false, null);

        return new LoadedJWS(
            $jws->getPayload(),
            $jws->verify($this->keyLoader->loadKey('public'), self::SIGNATUREALGORITHM),
            null !== $this->ttl
        );
    }
}
