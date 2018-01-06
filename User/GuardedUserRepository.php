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

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class GuardedUserRepository
 */
class GuardedUserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    /**
     *{@inheritdoc}
     */
    public function __construct(RegistryInterface $registry, string $userClass = '')
    {
        parent::__construct($registry, $userClass);
    }

    /**
     * @param string $userName
     *
     * @return UserInterface|null
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($userName)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere('u.userName = :username')
            ->setParameter('email', $userName)
            ->setParameter('username', $userName)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
