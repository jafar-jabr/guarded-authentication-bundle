<?php
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/21/2017
 * Time: 9:00 PM
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Provider;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Creator\CreatedJWS;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\KeyLoader\LoadedJWS;

interface JWSProviderInterface
{
    /**
     * Creates a new JWS signature from a given payload.
     *
     * @param array $payload
     *
     * @return CreatedJWS
     */
    public function create(array $payload);

    /**
     * Loads an existing JWS signature from a given JWT token.
     *
     * @param string $token
     *
     * @return LoadedJWS
     */
    public function load($token);
}