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
use PHPUnit\Framework\TestCase;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class ApiProblemTest
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Test\Api\ApiResponse
 */
final class ApiProblemTest extends TestCase
{
    public function testToArray()
    {
        $check = new ApiProblem(401);
        $result = $check->toArray();
        $this->assertEquals(is_array($result), true);
    }
}
