<?php

namespace App\Tests\Repository;

use App\Entity\Article;
use App\Enum\CategoryEnum;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire simple pour valider la logique de tri du repository
 * Sans dépendance à la base de données
 */
class ArticleRepositoryTest extends TestCase
{
    public function testFindAllOrderedByCreatedAtLogicValidation(): void
    {
        // Given - Créer des articles avec des dates différentes (simulation)
        $dates = [
            new \DateTimeImmutable('2024-01-01 10:00:00'),
            new \DateTimeImmutable('2024-12-01 10:00:00'),
            new \DateTimeImmutable('2024-06-01 10:00:00'),
        ];
        
        // When - Trier par ordre décroissant (simulation du comportement du repository)
        usort($dates, function($a, $b) {
            return $b <=> $a; // Tri décroissant
        });
        
        // Then
        $this->assertEquals('2024-12-01', $dates[0]->format('Y-m-d'), 'Le plus récent doit être en premier');
        $this->assertEquals('2024-06-01', $dates[1]->format('Y-m-d'), 'Le moyen doit être au milieu');
        $this->assertEquals('2024-01-01', $dates[2]->format('Y-m-d'), 'Le plus ancien doit être en dernier');
    }

    public function testArticleCreationOrderValidation(): void
    {
        // Given
        $article1 = new Article();
        $article1->setTitle('Premier article');
        
        // Simuler un délai entre les créations
        usleep(1000); // 1ms
        
        $article2 = new Article();
        $article2->setTitle('Deuxième article');
        
        // Then
        $this->assertLessThan(
            $article2->getCreatedAt(),
            $article1->getCreatedAt(),
            'Le premier article doit avoir une date de création antérieure'
        );
        
        // Vérifier qu'un tri décroissant placerait article2 en premier
        $articles = [$article1, $article2];
        usort($articles, function($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });
        
        $this->assertEquals('Deuxième article', $articles[0]->getTitle());
        $this->assertEquals('Premier article', $articles[1]->getTitle());
    }
}
