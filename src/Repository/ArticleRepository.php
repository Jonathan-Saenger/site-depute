<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findPublishedArticles(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isPublished = :published')
            ->setParameter('published', true)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPublishedArticlesByCategory(string $category): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isPublished = :published')
            ->andWhere('a.category = :category')
            ->setParameter('published', true)
            ->setParameter('category', $category)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
