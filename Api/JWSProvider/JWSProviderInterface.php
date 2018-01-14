<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSProvider;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSCreator\JWSCreator;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\LoadedJWS;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Interface JWSProviderInterface
 */
interface JWSProviderInterface
{
    /**
     * Creates a new JWS signature from a given payload.
     *
     * @param array $payload
     *
     * @return JWSCreator
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
