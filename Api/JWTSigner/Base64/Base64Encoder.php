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
 * Class Base64Encoder.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class Base64Encoder implements EncoderInterface
{
    /**
     * @param string $data
     *
     * @return string
     */
    public function encode($data)
    {
        return base64_encode($data);
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function decode($data)
    {
        return base64_decode($data);
    }
}
