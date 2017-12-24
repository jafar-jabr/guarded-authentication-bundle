<?php
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/21/2017
 * Time: 11:25 AM
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Extractor;

use Symfony\Component\HttpFoundation\Request;

final class TokenExtractor implements TokenExtractorInterface
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $prefix
     * @param string $name
     */
    public function __construct($prefix, $name)
    {
        $this->prefix = $prefix;
        $this->name   = $name;
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
