<?php

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api;

/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/8/2017
 * Time: 12:09 PM
 */
use Symfony\Component\HttpFoundation\Response;

class ApiProblem
{
    const VALIDATION_ERROR_TYPE = 'Validation_Error';
    const INVALID_REQUEST_BODY_FORMAT_TYPE = 'Invalid_body_format';
    private static $titles = [
        self::VALIDATION_ERROR_TYPE => 'There was a validation error',
        self::INVALID_REQUEST_BODY_FORMAT_TYPE => 'Invalid Json format sent'
    ];
    private $statusCode;
    private $type;
    private $title;
    private $detail;
    private $extranData = [];

    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;
        if ($type === null) {
            $this->type = 'about:blank';
            $this->title = Response::$statusTexts[$statusCode] ?? 'Unknown status code';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new\InvalidArgumentException('not= title for ' . $type);
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
            'type' => $this->type,
            'title' => $this->title,

        ];
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}