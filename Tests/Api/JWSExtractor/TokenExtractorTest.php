<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests\Api\JWSExtractor;

use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSExtractor\TokenExtractor;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class TokenExtractorTest
 */
final class TokenExtractorTest extends TestCase
{
    /**
     * test getRequestToken.
     */
    public function testGetTokenRequest()
    {
        $extractor = new TokenExtractor('Bearer', 'Authorization');

        $request = new Request();
        $this->assertFalse($extractor->extract($request));

        $request = new Request();
        $request->headers->set('Authorization', 'Bear testToken');
        $this->assertFalse($extractor->extract($request));

        $request = new Request();
        $request->headers->set('not Authorization', 'Bearer testToken');
        $this->assertFalse($extractor->extract($request));

        $request = new Request();
        $request->headers->set('Authorization', 'Bearer testToken');
        $this->assertEquals('testToken', $extractor->extract($request));
    }
}
