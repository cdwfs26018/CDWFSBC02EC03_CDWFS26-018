<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Salle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function isSalleDisponible(
        Salle $salle,
        \DateTimeInterface $dateDebut,
        \DateTimeInterface $dateFin
    ): bool {
        $qb = $this->createQueryBuilder('r');
        $qb->select('COUNT(r.id)')
            ->where('IDENTITY(r.salle) = :salleId')
            ->andWhere('r.dateDebut < :dateFin')
            ->andWhere('r.dateFin > :dateDebut')
            ->setParameter('salleId', $salle->getId()->toBinary())
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin);

        return (int) $qb->getQuery()->getSingleScalarResult() === 0;
    }


    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
