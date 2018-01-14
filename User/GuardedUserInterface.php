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
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Interface GuardedUserInterface
 * @package Jafar\Bundle\GuardedAuthenticationBundle\User
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
     * @return string $userName
     */
    public function getUserName();

    /**
     * @param string $userName
     *
     * @return self
     */
    public function setUserName(string $userName);

    /**
     * @return string $password
     */
    public function getPassword();

    /**
     * @param string $password
     *
     * @return self
     */
    public function setPassword(string $password);

    /**
     * @return array $roles
     */
    public function getRoles();

    /**
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles);
}
