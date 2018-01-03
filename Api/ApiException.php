<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class ApiException
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Api
 */
final class ApiException extends \Exception
{
    const INVALID_TOKEN    = 'invalid_token';
    const UNVERIFIED_TOKEN = 'unverified_token';
    const EXPIRED_TOKEN    = 'expired_token';
    const INVALID_CONFIG = 'invalid_config';
    const UNSIGNED_TOKEN = 'unsigned_token';

    /**
     * @var string
     */
    private $reason;

    /**
     * @param string          $reason
     * @param string          $message
     * @param \Exception|null $previous
     */
    public function __construct($reason, $message, \Exception $previous = null)
    {
        $this->reason = $reason;
        parent::__construct($message, 500, $previous);
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}
