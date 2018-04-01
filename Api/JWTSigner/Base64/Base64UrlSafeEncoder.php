<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Base64;

/**
 * Class Base64UrlSafeEncoder.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class Base64UrlSafeEncoder implements EncoderInterface
{
    public function encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function decode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
