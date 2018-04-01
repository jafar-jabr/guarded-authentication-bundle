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
 * Interface EncoderInterface.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
interface EncoderInterface
{
    /**
     * @param string $data
     *
     * @return string
     */
    public function encode($data);

    /**
     * @param string $data
     *
     * @return string
     */
    public function decode($data);
}
