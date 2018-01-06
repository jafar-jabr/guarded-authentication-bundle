<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Creator;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class CreatedJWS
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Creator
 */
class CreatedJWS
{
    const SIGNED = 'signed';

    /**
     * The JSON Web Token.
     *
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $state;

    /**
     * CreatedJWS constructor.
     * @param string $token
     * @param bool $isSigned
     */
    public function __construct(string $token, bool $isSigned)
    {
        $this->token = $token;
        if (true === $isSigned) {
            $this->state = self::SIGNED;
        }
    }

    /**
     * @return bool
     */
    public function isSigned()
    {
        return self::SIGNED === $this->state;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
