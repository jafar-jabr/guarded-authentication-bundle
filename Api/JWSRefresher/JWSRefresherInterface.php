<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSRefresher;

use Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface JWSRefresherInterface.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
interface JWSRefresherInterface
{
    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws ApiException
     */
    public function decode(Request $request);
}
