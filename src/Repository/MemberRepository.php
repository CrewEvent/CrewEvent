<?php

namespace App\Repository;

use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Member>
 *
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    public function save(Member $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Member $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Member[] Returns an array of Member objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Member
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    //Pareil pour l'entité PrivateMessage On cherche juste les messages passés avec l'utre utilisateur

    public function findExchange($username, $otherUsername): ?Member
    {
        return $this->createQueryBuilder('p')
            ->andwhere('p.username = :username and p.otherUsername = :otherUsername or (p.username = :otherUsername and p.otherUsername = :username)')
            ->setParameter('username', $username)
            ->setParameter('otherUsername', $otherUsername)
            ->getQuery()
            ->getOneOrNullResult();
    }


    //On récupére ici juste les fils de discussions passés par cet utilisateur

    /**
     * @return Member[] Returns an array of PrivateMessage objects
     */
    public function findMessages($username): array
    {
        return $this->createQueryBuilder('p')
            ->andwhere('p.username = :username or p.otherUsername = :username')
            ->setParameter('username', $username)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}