<?php

namespace Jafar\Bundle\GuardedAuthenticationBundle\Services;

use Doctrine\ORM\EntityManagerInterface;

class UserProviderService
{

    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    public static function getUser($username)
    {
        return 1;
    }
}
