<?php
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/21/2017
 * Time: 9:59 AM
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\Extractor;

use Symfony\Component\HttpFoundation\Request;

interface TokenExtractorInterface
{
    /**
     * @param Request $request
     *
     * @return string|false
     */
    public function extract(Request $request);
}