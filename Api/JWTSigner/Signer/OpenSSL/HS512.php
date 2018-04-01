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
 * Class HS512.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class HS512 extends HMAC
{
    public function getHashingAlgorithm()
    {
        return 'sha512';
    }
}
