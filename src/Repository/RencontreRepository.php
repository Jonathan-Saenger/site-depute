<?php

namespace App\Repository;

use App\Entity\Rencontre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rencontre>
 */
class RencontreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rencontre::class);
    }

    public function findCommunes(): array
    {
        return $this->createQueryBuilder('r')
            ->select('DISTINCT r.commune')
            ->orderBy('r.commune', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function findTypes(): array
    {
        return $this->createQueryBuilder('r')
            ->select('DISTINCT r.type')
            ->orderBy('r.type', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Récupère les rendez-vous visibles et triés par date
     */

     public function findUpcomingRencontre(?string $commune = null, ?string $type = null): array
     {
        $qb = $this->createQueryBuilder('r')
            ->where('r.date >= :now')
            ->setParameter('now', new \DateTimeImmutable());

        // Utiliser LIKE pour commune avec paramètre exacte
        if ($commune && $commune !== '') {
            $qb->andWhere('r.commune = :commune')
               ->setParameter('commune', $commune);
        }

        // Utiliser LIKE pour type avec paramètre exacte
        if ($type && $type !== '') {
            $qb->andWhere('r.type = :type')
               ->setParameter('type', $type);
        }

        return $qb->orderBy('r.date', 'ASC')
                  ->getQuery()
                  ->getResult();
     }
}
