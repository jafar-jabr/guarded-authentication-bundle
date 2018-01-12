<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class TestCase
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Tests
 */
abstract class TestCase extends WebTestCase
{
    protected static $client;

    public function test()
    {
        return true;
    }
}
