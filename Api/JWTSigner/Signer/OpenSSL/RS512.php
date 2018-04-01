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
 * Class RS512.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class RS512 extends RSA
{
    public function getHashingAlgorithm()
    {
        return version_compare(phpversion(), '5.4.8', '<') ? 'SHA512' : OPENSSL_ALGO_SHA512;
    }
}
