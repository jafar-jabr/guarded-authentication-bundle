<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests\Api\ApiResponse;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse\ApiProblem;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\ApiResponse\ApiResponseFactory;
use PHPUnit\Framework\TestCase;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class ApiResponseFactoryTest
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Tests\Api\ApiResponse
 */
final class ApiResponseFactoryTest extends TestCase
{
    public function testCreateResponse()
    {
        $apiProblem      = new ApiProblem(401);
        $responseFactory = new ApiResponseFactory();
        $response        = $responseFactory->createResponse($apiProblem);
        $headerType      = $response->headers->get('Content-Type');
        $this->assertEquals($headerType, 'application/problem+json');
    }
}
