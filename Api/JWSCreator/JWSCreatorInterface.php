<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSCreator;

/**
 * Interface JWSCreatorInterface.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
interface JWSCreatorInterface
{
    /**
     * @return bool
     */
    public function isSigned();

    /**
     * @return string
     */
    public function getToken();
}
