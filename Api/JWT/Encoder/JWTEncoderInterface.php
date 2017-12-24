<?php
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/21/2017
 * Time: 10:20 AM
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Encoder;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiException;

interface JWTEncoderInterface
{
    /**
     * @param array $data
     *
     * @return string the encoded token string
     *
     * @throws ApiException If an error occurred while trying to create
     *                                   the token (invalid crypto key, invalid payload...)
     */
    public function encode(array $data);

    /**
     * @param string $token
     *
     * @return array
     *
     * @throws ApiException If an error occurred while trying to load the token
     *                                   (invalid signature, invalid crept key, expired token...)
     */
    public function decode($token);
}
