<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Extractor;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Interface TokenExtractorInterface
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Extractor
 */
interface TokenExtractorInterface
{
    /**
     * @param Request $request
     *
     * @return string|false
     */
    public function extract(Request $request);
}
