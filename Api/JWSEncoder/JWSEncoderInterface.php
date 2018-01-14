<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSEncoder;

use Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Interface JWSEncoderInterface
 */
interface JWSEncoderInterface
{
    /**
     * @param array $data
     *
     * @return string the encoded token string
     *
     * @throws ApiException If an error occurred while trying to create
     *                      the token (invalid crypto key, invalid payload...)
     */
    public function encode(array $data);

    /**
     * @param string $token
     *
     * @return array
     *
     * @throws ApiException If an error occurred while trying to load the token
     *                      (invalid signature, invalid crept key, expired token...)
     */
    public function decode($token);
}
