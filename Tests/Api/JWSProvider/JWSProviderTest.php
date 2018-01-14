<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests\Api\JWSProvider;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSCreator\JWSCreator;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSProvider\JWSProvider;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\KeyLoaderInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\LoadedJWS;
use PHPUnit\Framework\TestCase;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class JWSProviderTest
 */
class JWSProviderTest extends TestCase
{
    const PRIVATE_KEY = '
-----BEGIN ENCRYPTED PRIVATE KEY-----
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
D5LySC0J8wgebXtJlzqJxQ==
-----END ENCRYPTED PRIVATE KEY-----
';

    const PUBLIC_KEY = '
-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA0WjReuizJ9YnRCMF+FSV
aXBGMj8iEBvKcE7d863WfLk74EsLuRO2emeWeggsDzRmHKRz/aO+uXwzlIfJEvbA
z18MnhnbK9zrXhgEaLsLcw/ibP2Kwzjf3Z2vsKn9MS8PPWEbZFTpoxIDnys2Z2gK
WP4FCHz8tRo8RFwQ45jWH5JziOS/zNu5ONad91jCoz6D3R+4avoaVBFh9ibgZwb4
LGj9Md4kGvWkW+nPAWO7e0/YDQrPvinLQjprYAfvpkfzKHPpH01PcVYThr7QA5Xw
Hr82iIx0gFJut6eYGq1E+gQTm5VefSAFd84fsiWF/juXo1G/4gHUbKuOetf988m7
eUPewzA/L9gQEyUgqy9tx+zThzHKJNvHkjS6+NDyaMAPoFOl8+oBltWVfLcfmPS+
y+dMUBxns/I7YZ2PV8vttFThDZqw84hqBIrs7P1Pu3/5y1ozGAkTWty7js+WT87Z
aJCOf1GsrT/8of5ug1j1p4flIbQ/y3C2A9TBufURoY6BHXtObhR4JNufiGJqaU5c
DxOt9qoZmUhkFHIm/hmzWV3+qnrRdj5uMuHPQ87OaQYTo8CCuykLptSYmw6yuWQS
5zSC/uW+o+ItuvMaFvwxLpd2g3Gp+xNYfkJy60oZ092qSojZRM0hnJPmb1Po/uYg
8BPdWv11mKmSwyrrStyjhAECAwEAAQ==
-----END PUBLIC KEY-----
';

    protected static $keyLoaderClass;

    /**
     * Tests to create a signed JWT Token.
     */
    public function testCreate()
    {
        $keyLoaderMock = $this->getKeyLoaderMock();
        $keyLoaderMock
            ->expects($this->once())
            ->method('loadKey')
            ->with('private')
            ->willReturn(self::PRIVATE_KEY);
        $keyLoaderMock
            ->expects($this->once())
            ->method('getPassphrase')
            ->willReturn('anyPassphrase');

        $payload     = ['username' => 'jafaronly'];
        $jwsProvider = new JWSProvider($keyLoaderMock, 3600);

        $this->assertInstanceOf(JWSCreator::class, $created = $jwsProvider->create($payload));

        return $created->getToken();
    }

    /**
     * Tests to verify the signature of a valid given JWT Token.
     *
     * @depends testCreate
     */
    public function testLoad($jwt)
    {
        $keyLoaderMock = $this->getKeyLoaderMock();
        $keyLoaderMock
            ->expects($this->once())
            ->method('loadKey')
            ->with('public')
            ->willReturn(self::PUBLIC_KEY);

        $jwsProvider = new JWSProvider($keyLoaderMock, 3600);
        $loadedJWS   = $jwsProvider->load($jwt);
        $this->assertInstanceOf(LoadedJWS::class, $loadedJWS);

        $payload = $loadedJWS->getPayload();
        $this->assertTrue(isset($payload['exp']));
        $this->assertTrue(isset($payload['iat']));
        $this->assertTrue(isset($payload['username']));
    }

    public function testAllowEmptyTtl()
    {
        $keyLoaderMock = $this->getKeyLoaderMock();
        $keyLoaderMock
            ->expects($this->at(0))
            ->method('loadKey')
            ->with('private')
            ->willReturn(self::PRIVATE_KEY);
        $keyLoaderMock
            ->expects($this->at(1))
            ->method('getPassphrase')
            ->willReturn('anyPassphrase');

        $keyLoaderMock
            ->expects($this->at(2))
            ->method('loadKey')
            ->with('public')
            ->willReturn(self::PUBLIC_KEY);

        $provider = new JWSProvider($keyLoaderMock);
        $jws      = $provider->create(['username' => 'jafaronly']);

        $this->assertInstanceOf(JWSCreator::class, $jws);
        $this->assertTrue($jws->isSigned());

        $jws = $provider->load($jws->getToken());

        $this->assertInstanceOf(LoadedJWS::class, $jws);
        $this->assertFalse($jws->isInvalid());
        $this->assertFalse($jws->isExpired());
        $this->assertTrue($jws->isVerified());
        $this->assertArrayNotHasKey('exp', $jws->getPayload());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The TTL should be a numeric value
     */
    public function testInvalidTtl()
    {
        new JWSProvider($this->getKeyLoaderMock(), 'string_ttl');
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getKeyLoaderMock()
    {
        return $this
            ->getMockBuilder(KeyLoaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
