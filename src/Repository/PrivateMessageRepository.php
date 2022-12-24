<?php

namespace App\Repository;

use App\Entity\PrivateMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrivateMessage>
 *
 * @method PrivateMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrivateMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrivateMessage[]    findAll()
 * @method PrivateMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrivateMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivateMessage::class);
    }

    public function save(PrivateMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PrivateMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return PrivateMessage[] Returns an array of PrivateMessage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PrivateMessage
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    //On crée notre propre querry builder pour récupérer tous les messages échangés par cet utilisateur avec 1 autre

    /**
     * @return PrivateMessage[] Returns an array of PrivateMessage objects
     */
    public function findMyMessage($sender, $receiver): array
    {
        return $this->createQueryBuilder('p')
            ->andwhere('p.sender = :sender or p.receiver = :receiver')
            ->setParameter('sender', $sender)
            ->setParameter('receiver', $receiver)
            ->orderBy('p.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
    //Ce querry est pour trouver les échanges que l'utilisateur a fait avec cet autre utilisateur

    /**
     * @return PrivateMessage[] Returns an array of PrivateMessage objects
     */
    public function findExchange($sender, $receiver): array
    {
        return $this->createQueryBuilder('p')
            ->andwhere('p.sender = :sender and p.receiver = :receiver or (p.sender = :receiver and p.receiver = :sender)')
            ->setParameter('sender', $sender)
            ->setParameter('receiver', $receiver)
            ->orderBy('p.createdAt', 'ASC')
/*            ->setMaxResults(10)*/
            ->getQuery()
            ->getResult();
    }
}