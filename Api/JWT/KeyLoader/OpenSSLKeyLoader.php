<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\KeyLoader;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Date: 11/02/2017
 */

class OpenSSLKeyLoader extends AbstractKeyLoader
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If the key cannot be read
     * @throws \RuntimeException Either the key or the passPhrase is not valid
     */
    public function loadKey($type)
    {
        $path         = $this->getKeyPath($type);
        $encryptedKey = file_get_contents($path);
        $key          = call_user_func_array(
            sprintf('openssl_pkey_get_%s', $type),
            self::TYPE_PRIVATE == $type ? [$encryptedKey, $this->getPassPhrase()] : [$encryptedKey]
        );

        if (!$key) {
            $sslError = '';
            while ($msg = trim(openssl_error_string(), " \n\r\t\0\x0B\"")) {
                if ('error:' === substr($msg, 0, 6)) {
                    $msg = substr($msg, 6);
                }
                $sslError .= "\n ".$msg;
            }

            throw new \RuntimeException(
                sprintf('Failed to load %s key "%s": %s', $type, $path, $sslError)
            );
        }

        return $key;
    }
}
