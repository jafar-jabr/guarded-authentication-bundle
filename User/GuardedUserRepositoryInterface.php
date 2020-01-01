<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\User;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface GuardedUserRepositoryInterface.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
interface GuardedUserRepositoryInterface extends UserLoaderInterface
{
    /**
     * Loads the user for the given $userName.
     *
     * This method must return null if the user is not found.
     *
     * @param string $userName The user property of your choice (ei. username, email or even id)
     *
     * @return UserInterface|null
     */
    public function loadUserByUsername(string $userName);
}
