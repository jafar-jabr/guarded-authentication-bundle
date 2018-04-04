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

/**
 * Class RSA.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
abstract class RSA extends PublicKey
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedPrivateKeyType()
    {
        return defined('OPENSSL_KEYTYPE_RSA') ? OPENSSL_KEYTYPE_RSA : '';
    }
}
