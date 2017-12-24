<?php
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/21/2017
 * Time: 7:56 PM
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api;

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

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}
