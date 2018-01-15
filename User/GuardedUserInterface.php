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

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Interface GuardedUserInterface.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
interface GuardedUserInterface extends AdvancedUserInterface
{
    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email);

    /**
     * @return string $email
     */
    public function getEmail();

    /**
     * @param string $username
     *
     * @return self
     */
    public function setUsername(string $username);

    /**
     * @param string $password
     *
     * @return self
     */
    public function setPassword(string $password);

    /**
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles);
}
