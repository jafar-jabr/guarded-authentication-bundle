<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests\Api\JWSEncoder;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSCreator\JWSCreator;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSEncoder\JWSEncoder;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSProvider\JWSProviderInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader\LoadedJWS;
use PHPUnit\Framework\TestCase;

/**
 * Class JWSEncoderTest
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class JWSEncoderTest extends TestCase
{
    /**
     * Tests calling JWSEncoder::decode() with a valid signature and payload.
     */
    public function testDecodeFromValidJWS()
    {
        $payload = [
            'username' => 'jafaronly',
            'exp'      => time() + 3600,
        ];

        $loadedJWS   = new LoadedJWS($payload, true);
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('load')
            ->willReturn($loadedJWS);

        $encoder = new JWSEncoder($jwsProvider);

        $this->assertSame($payload, $encoder->decode('jwt'));
    }

    /**
     * Tests calling JWSEncoder::encode() with a signed token.
     */
    public function testEncodeFromValidJWS()
    {
        $createdJWS  = new JWSCreator('jwt', true);
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('create')
            ->willReturn($createdJWS);

        $encoder = new JWSEncoder($jwsProvider);

        $this->assertSame('jwt', $encoder->encode([]));
    }

    /**
     * Tests that calling JWSEncoder::encode() with an unsigned JWS correctly fails.
     *
     * @expectedException \Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException
     */
    public function testEncodeFromUnsignedJWS()
    {
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('create')
            ->willReturn(new JWSCreator('jwt', false));

        $encoder = new JWSEncoder($jwsProvider);
        $encoder->encode([]);
    }

    /**
     * Tests that calling JWSEncoder::decode() with an unverified signature correctly fails.
     *
     * @expectedException \Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException
     */
    public function testDecodeFromUnverifiedJWS()
    {
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('load')
            ->willReturn(new LoadedJWS([], false));

        $encoder = new JWSEncoder($jwsProvider);
        $encoder->decode('something');
    }

    /**
     * Tests that calling JWSEncoder::decode() with an expired payload correctly fails.
     *
     * @expectedException        \Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException
     * @expectedExceptionMessage Expired JWT Token
     */
    public function testDecodeFromExpiredPayload()
    {
        $loadedJWS   = new LoadedJWS(['exp' => time() - 3600], true);
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('load')
            ->willReturn($loadedJWS);

        $encoder = new JWSEncoder($jwsProvider);
        $encoder->decode('jwt');
    }

    /**
     * Tests that calling JWSEncoder::decode() with an iat set in the future correctly fails.
     *
     * @expectedException        \Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException
     * @expectedExceptionMessage Invalid JWT Token
     */
    public function testDecodeWithInvalidIssudAtClaimInPayload()
    {
        $loadedJWS   = new LoadedJWS(['exp' => time() + 3600, 'iat' => time() + 3600], true);
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('load')
            ->willReturn($loadedJWS);
        $encoder = new JWSEncoder($jwsProvider);
        $encoder->decode('jwt');
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getJWSProviderMock()
    {
        return $this->getMockBuilder(JWSProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
