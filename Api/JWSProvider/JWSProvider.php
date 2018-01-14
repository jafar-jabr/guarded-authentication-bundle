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
use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\KeyLoaderInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\LoadedJWS;
use Namshi\JOSE\JWS;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class JWSProvider
 */
class JWSProvider implements JWSProviderInterface
{
    const CRYPTIONENGINE = 'OpenSSL';

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
     * @param KeyLoaderInterface $keyLoader
     * @param int | null               $ttl
     *
     * @throws \InvalidArgumentException If the given ttl is not numeric
     */
    public function __construct(KeyLoaderInterface $keyLoader, $ttl = null)
    {
        if (null != $ttl && !is_numeric($ttl)) {
            throw new \InvalidArgumentException(sprintf('The TTL should be a numeric value, got %s instead.', $ttl));
        }
        $this->keyLoader = $keyLoader;
        $this->ttl       = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $payload)
    {
        $jws    = new JWS(['alg' => self::SIGNATUREALGORITHM], self::CRYPTIONENGINE);
        $claims = ['iat' => time()];
        if (null !== $this->ttl) {
            $claims['exp'] = time() + $this->ttl;
        }
        $jws->setPayload($payload + $claims);
        $jws->sign(
            $this->keyLoader->loadKey('private'),
            $this->keyLoader->getPassphrase()
        );

        return new JWSCreator($jws->getTokenString(), $jws->isSigned());
    }

    /**
     * {@inheritdoc}
     */
    public function load($token)
    {
        $jws = JWS::load($token, false, null, self::CRYPTIONENGINE);

        return new LoadedJWS(
            $jws->getPayload(),
            $jws->verify($this->keyLoader->loadKey('public'), self::SIGNATUREALGORITHM),
            null !== $this->ttl
        );
    }
}
