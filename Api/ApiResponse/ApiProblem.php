<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class ApiProblem
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse
 */
final class ApiProblem
{
    const VALIDATION_ERROR_TYPE = 'Validation_Error';

    const INVALID_REQUEST_BODY_FORMAT_TYPE = 'Invalid_body_format';

    private static $titles = [
        self::VALIDATION_ERROR_TYPE             => 'There was a validation error',
        self::INVALID_REQUEST_BODY_FORMAT_TYPE  => 'Invalid Json format sent',
    ];

    private $statusCode;

    private $type;

    private $title;

    private $detail;

    /**
     * ApiProblem constructor.
     *
     * @param $statusCode
     * @param null $type
     */
    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;
        if (null === $type) {
            $this->type  = 'about:blank';
            $this->title = isset(Response::$statusTexts[$statusCode]) ?
                Response::$statusTexts[$statusCode] : 'Unknown status code';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new\InvalidArgumentException('not title for ' . $type);
            }
        }
    }

    public function set($key, $value)
    {
        $this->$key = $value;
    }

    public function toArray()
    {
        return [
            'detail' => $this->detail,
            'status' => $this->statusCode,
            'type'   => $this->type,
            'title'  => $this->title,
        ];
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
