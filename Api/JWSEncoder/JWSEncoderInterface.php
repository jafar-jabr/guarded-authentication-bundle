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
 * Interface JWSEncoderInterface.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
interface JWSEncoderInterface
{
    /**
     * @param array  $data
     * @param string $type
     *
     * @return string the encoded token string
     *
     * @throws ApiException If an error occurred while trying to create
     *                      the token (invalid cryptic key, invalid payload...)
     */
    public function encode(array $data, string $type): string;

    /**
     * @param string $token
     *
     * @return array
     *
     * @throws ApiException If an error occurred while trying to load the token
     *                      (invalid signature, invalid crept key, expired token...)
     */
    public function decode(string $token): array;
}
