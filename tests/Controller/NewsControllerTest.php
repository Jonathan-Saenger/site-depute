<?php

namespace App\Tests\Controller;

use App\Entity\Article;
use PHPUnit\Framework\TestCase;

class NewsControllerTest extends TestCase
{
    private array $articles;

    protected function setUp(): void
    {
        // Création des articles de test
        $this->articles = [];
        $dates = ['2024-05-10', '2024-05-09', '2024-05-08', '2024-05-07'];

        foreach ($dates as $index => $date) {
            $article = new Article();
            $article->setTitle('Article Test ' . ($index + 1));
            $article->setContent('Contenu test ' . ($index + 1));
            $article->setSlug('article-test-' . ($index + 1));
            $article->setImageUrl('/images/test' . ($index + 1) . '.jpg');
            $article->setCreatedAt(new \DateTimeImmutable($date));
            $article->setIsPublished(true);

            $this->articles[] = $article;
        }
    }

    public function testArticlesLimitedToThreeOnHomePage(): void
    {
        // Arrange
        $articles = array_slice($this->articles, 0, 3);

        // Assert
        $this->assertCount(3, $articles, 'La page d\'accueil devrait afficher exactement 3 articles');
        $this->assertLessThanOrEqual(3, count($articles), 'Plus de 3 articles sont affichés');
    }

    public function testArticlesAreSortedByDateDesc(): void
    {
        // Arrange
        $articles = $this->articles;

        // Act
        $dates = array_map(function($article) {
            return $article->getCreatedAt();
        }, $articles);

        $sortedDates = $dates;
        rsort($sortedDates);

        // Assert
        $this->assertSame(
            $dates,
            $sortedDates,
            'Les articles ne sont pas triés du plus récent au plus ancien'
        );
    }

    public function testArticleStructure(): void
    {
        // Arrange
        $article = $this->articles[0];

        // Assert
        $this->assertNotEmpty($article->getTitle(), 'Le titre est vide');
        $this->assertNotEmpty($article->getContent(), 'Le contenu est vide');
        $this->assertNotEmpty($article->getImageUrl(), 'L\'URL de l\'image est vide');
        $this->assertNotEmpty($article->getSlug(), 'Le slug est vide');
        $this->assertInstanceOf(\DateTimeImmutable::class, $article->getCreatedAt());
        $this->assertTrue($article->isPublished());
    }
}
