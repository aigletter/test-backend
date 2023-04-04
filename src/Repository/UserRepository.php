<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByAuthToken(string $token)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT u
            FROM App\Entity\User u
            INNER JOIN u.tokens t
            WHERE t.token = :token"
        )->setParameter('token', $token);

        return $query->getOneOrNullResult();
    }

    public function findOneByCredentials(string $login, string $password)
    {
        return $this->findOneBy([
            'login' => $login,
            // I won't use md5 algorithm in real projects ;)
            'password' => md5($password),
        ]);
    }
}
