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

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class TokenExtractor
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSExtractor
 */
final class TokenExtractorTestTest implements TokenExtractorInterfaceTest
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $prefix
     * @param string $name
     */
    public function __construct(string $prefix, string $name)
    {
        $this->prefix = $prefix;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        if (!$request->headers->has($this->name)) {
            return false;
        }
        $authorizationHeader = $request->headers->get($this->name);
        if (empty($this->prefix)) {
            return $authorizationHeader;
        }
        $headerParts = explode(' ', $authorizationHeader);
        if (!(2 === count($headerParts) && $headerParts[0] === $this->prefix)) {
            return false;
        }

        return $headerParts[1];
    }
}
