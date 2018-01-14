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

use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\OpenSSLKeyLoader;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class OpenSSLKeyLoaderTest
 */
class OpenSSLKeyLoaderTest extends AbstractTestKeyLoader
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $keys_path       = dirname(__FILE__).'\keys\\';
        $this->keyLoader = new OpenSSLKeyLoader('anyPassphrase', $keys_path);
    }

    /**
     * @expectedException        \RuntimeException
     */
    public function testLoadInvalidPublicKey()
    {
        touch('public.pem');

        $this->keyLoader->loadKey('public');
    }

    /**
     * @expectedException        \RuntimeException
     */
    public function testLoadInvalidPrivateKey()
    {
        touch('private.pem');

        $this->keyLoader->loadKey('private');
    }
}
