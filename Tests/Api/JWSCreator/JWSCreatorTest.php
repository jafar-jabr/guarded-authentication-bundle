<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests\Api\JWSCreator;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSCreator\JWSCreator;
use PHPUnit\Framework\TestCase;

/**
 * Class JWSCreatorTest.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class JWSCreatorTest extends TestCase
{
    const TOKEN = 'test Token';

    public function testCreateSignedJTW()
    {
        $jws = new JWSCreator(self::TOKEN, true);

        $this->assertSame(self::TOKEN, $jws->getToken());
        $this->assertTrue($jws->isSigned());
    }

    public function testCreateUnsignedJWT()
    {
        $jws = new JWSCreator(self::TOKEN, false);

        $this->assertSame(self::TOKEN, $jws->getToken());
        $this->assertFalse($jws->isSigned());
    }
}
