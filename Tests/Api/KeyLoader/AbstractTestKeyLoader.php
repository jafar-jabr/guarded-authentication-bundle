<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests\Api\KeyLoader;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\KeyLoaderInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTestKeyLoader.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
abstract class AbstractTestKeyLoader extends TestCase
{
    /** @var KeyLoaderInterface */
    protected $keyLoader;

    /**
     * Test load key from invalid type.
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The key type must be "public" or "private", "wrongType" given.
     */
    public function testLoadKeyFromWrongType()
    {
        $this->keyLoader->loadKey('wrongType');
    }
}
