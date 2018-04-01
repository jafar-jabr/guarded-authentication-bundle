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

use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\LoadedJWS;
use PHPUnit\Framework\TestCase;

/**
 * Class LoadedJWSTest.
 *
 *  @author Jafar Jabr <jafaronly@yahoo.com>
 */
class LoadedJWSTest extends TestCase
{
    private $goodPayload;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->goodPayload = [
            'username' => 'jafaronly',
            'exp'      => time() + 3600,
            'iat'      => time(),
        ];
    }

    public function testVerifiedWithEmptyPayload()
    {
        $jws = new LoadedJWS($payload = [], true);

        $this->assertSame($payload, $jws->getPayload());
        $this->assertFalse($jws->isVerified());
        $this->assertFalse($jws->isExpired());
    }

    public function testUnverifiedWithGoodPayload()
    {
        $jws = new LoadedJWS($this->goodPayload, false);

        $this->assertSame($this->goodPayload, $jws->getPayload());
        $this->assertFalse($jws->isExpired());
        $this->assertFalse($jws->isVerified());
    }

    public function testVerifiedWithGoodPayload()
    {
        $jws = new LoadedJWS($this->goodPayload, true);

        $this->assertSame($this->goodPayload, $jws->getPayload());
        $this->assertFalse($jws->isExpired());
        $this->assertTrue($jws->isVerified());
    }

    public function testVerifiedWithExpiredPayload()
    {
        $payload = $this->goodPayload;
        $payload['exp'] -= 3600;

        $jws = new LoadedJWS($payload, true);

        $this->assertFalse($jws->isVerified());
        $this->assertTrue($jws->isExpired());
    }

    public function testIsInvalidReturnsTrueWithIssuedAtSetInTheFuture()
    {
        $payload = $this->goodPayload;
        $payload['iat'] += 3600;

        $jws = new LoadedJWS($payload, true);

        $this->assertFalse($jws->isVerified());
        $this->assertFalse($jws->isExpired());
        $this->assertTrue($jws->isInvalid());
    }
}
