<?php

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests\Api\KeyLoader;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\KeyLoaderInterface;
use PHPUnit\Framework\TestCase;

/**
 * Base class for KeyLoader classes tests.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
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
