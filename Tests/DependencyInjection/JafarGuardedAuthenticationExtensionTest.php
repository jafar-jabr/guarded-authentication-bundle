<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;

/**
 * Class JafarGuardedAuthenticationExtensionTest
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class JafarGuardedAuthenticationExtensionTest extends TestCase
{
    /**
     * Test Extension.
     */
    public function testExtension()
    {
        $this->assertEquals(1, 1);
    }
}
