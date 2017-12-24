<?php
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/21/2017
 * Time: 9:12 PM
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Provider;

use Namshi\JOSE\JWS;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\KeyLoader\KeyLoaderInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Creator\CreatedJWS;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\KeyLoader\LoadedJWS;

class JWSProvider implements JWSProviderInterface
{
    const CRYPTIONENGINE = 'OpenSSL';
    const SIGNATUREALGORITHM ='RS256';

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
     * @param int $ttl
     *
     * @throws \InvalidArgumentException If the given algorithm is not supported
     */
    public function __construct(KeyLoaderInterface $keyLoader, $ttl)
    {
        if (null !== $ttl && !is_numeric($ttl)) {
            throw new \InvalidArgumentException(sprintf('The TTL should be a numeric value, got %s instead.', $ttl));
        }
        $cryptoEngine = self::CRYPTIONENGINE;
        $signatureAlgorithm = self::SIGNATUREALGORITHM;
        if (!$this->isAlgorithmSupportedForEngine($cryptoEngine, $signatureAlgorithm)) {
            throw new \InvalidArgumentException(
                sprintf('The algorithm "%s" is not supported for %s', $signatureAlgorithm, $cryptoEngine)
            );
        }
        $this->keyLoader  = $keyLoader;
        $this->ttl        = $ttl;
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
        return new CreatedJWS($jws->getTokenString(), $jws->isSigned());
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

    /**
     * @param string $cryptoEngine
     * @param string $signatureAlgorithm
     *
     * @return bool
     */
    private function isAlgorithmSupportedForEngine($cryptoEngine, $signatureAlgorithm)
    {
        $signerClass = sprintf('Namshi\\JOSE\\Signer\\%s\\%s', $cryptoEngine, $signatureAlgorithm);
        return class_exists($signerClass);
    }
}
