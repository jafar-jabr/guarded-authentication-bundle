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
 * Class ApiProblem.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
final class ApiProblem
{
    const VALIDATION_ERROR_TYPE            = 'Validation_Error';

    const INVALID_REQUEST_BODY_FORMAT_TYPE = 'Invalid_body_format';

    /**
     * @var array
     */
    private static $titles = [
        self::VALIDATION_ERROR_TYPE             => 'There was a validation error',
        self::INVALID_REQUEST_BODY_FORMAT_TYPE  => 'Invalid Json format sent',
    ];

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $detail;

    /**
     * ApiProblem constructor.
     *
     * @param string $statusCode
     * @param null | string $type
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
                throw new\InvalidArgumentException('not title for '.$type);
            }
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'detail' => $this->detail,
            'status' => $this->statusCode,
            'type'   => $this->type,
            'title'  => $this->title,
        ];
    }

    /**
     * @return int|string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
