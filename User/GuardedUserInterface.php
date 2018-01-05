<?php
/**
 * @author Jafar Jabr <jafar.jabr@punct.ro>
 * Date: 12/26/2017
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\User;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface GuardedUserInterface extends AdvancedUserInterface
{
    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email);

    /**
     * @return string user's email
     */
    public function getEmail();

    /**
     * @return string user's userName
     */
    public function getUserName();

    /**
     * @param string user's userName
     * @return self
     */
    public function setUserName(string $userName);

    /**
     * @return string user's password
     */
    public function getPassword();

    /**
     * @param string $password
     * @return self
     */
    public function setPassword(string $password);

    /**
     * @return array user's roles
     */
    public function getRoles();

    /**
     * @param array $roles
     * @return self
     */
    public function setRoles(array $roles);
}