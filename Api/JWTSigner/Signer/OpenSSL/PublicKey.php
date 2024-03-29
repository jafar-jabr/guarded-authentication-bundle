<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Signer\OpenSSL;

use InvalidArgumentException;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Signer\SignerInterface;
use OpenSSLAsymmetricKey;
use RuntimeException;

/**
 * Class HS512.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class handle sign inputs with the a public key algorithm, after hashing it.
 */
abstract class PublicKey implements SignerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sign($input, $key, $password = null)
    {
        $keyResource = $this->getKeyResource($key, $password);
        if (!$this->supportsKey($keyResource)) {
            throw new InvalidArgumentException('Invalid key supplied.');
        }

        $signature = null;
        openssl_sign($input, $signature, $keyResource);

        return $signature;
    }

    /**
     * {@inheritdoc}
     */
    public function verify($key, $signature, $input)
    {
        $keyResource = $this->getKeyResource($key);
        if (!$this->supportsKey($keyResource)) {
            throw new InvalidArgumentException('Invalid key supplied.');
        }

        $result = openssl_verify($input, $signature, $keyResource);

        if (-1 === $result) {
            throw new RuntimeException('Unknown error during verification.');
        }

        return (bool) $result;
    }

    /**
     * Converts a string representation of a key into an OpenSSL resource.
     *
     * @param string|resource $key
     * @param string          $password
     *
     * @return OpenSSLAsymmetricKey|resource OpenSSL key resource
     */
    protected function getKeyResource($key, $password = null)
    {
        if (is_resource($key) || $key instanceof OpenSSLAsymmetricKey) {
            return $key;
        }
        $resource = $password ? openssl_pkey_get_private($key, $password): openssl_pkey_get_public($key);
        if (false === $resource) {
            throw new RuntimeException('Could not read key resource: '.openssl_error_string());
        }
        return $resource;
    }

    /**
     * Check if the key is supported by this signer.
     *
     * @param resource $key Public or private key
     *
     * @return bool
     */
    protected function supportsKey($key)
    {
        // OpenSSL 0.9.8+
        $keyDetails = openssl_pkey_get_details($key);

        return isset($keyDetails['type']) ? $this->getSupportedPrivateKeyType() === $keyDetails['type'] : false;
    }

    /**
     * Returns the private key type supported in this signer.
     *
     * @return string
     */
    abstract protected function getSupportedPrivateKeyType();
}
