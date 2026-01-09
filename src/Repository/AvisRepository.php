<?php

namespace App\Repository;

use App\Entity\Avis;
use App\Entity\Evenement;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    /**
     * Avis VALIDÉS pour un événement
     */
    public function findValidatedByEvenement(Evenement $evenement): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.evenement = :evenement')
            ->andWhere('a.accepte = true')
            ->setParameter('evenement', $evenement)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function debugAll(): array
    {
        return $this->createQueryBuilder('a')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si un utilisateur a déjà laissé un avis
     */
    public function userHasAlreadyReviewed(Evenement $evenement, User $user): bool
    {
        return (int) $this->createQueryBuilder('a')
                ->select('COUNT(a.id)')
                ->where('a.evenement = :evenement')
                ->andWhere('a.auteur = :user')
                ->setParameter('evenement', $evenement)
                ->setParameter('user', $user)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }
}
