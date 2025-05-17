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

    /**
     * Récupère les rendez-vous visibles et triés par date
     */

     public function findUpcomingRencontre(): array
     {
        return $this->createQueryBuilder('r')
            ->where('r.visible = :visible')
            ->andWhere('r.date >= :now')
            ->setParameter('visible', true)
            ->setParameter('now', new \DateTime())
            ->orderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
     }
}
