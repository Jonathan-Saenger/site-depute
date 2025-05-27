<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Enum\CategoryEnum;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function testArticleConstructorInitializesCreatedAtAndIsPublished(): void
    {
        // Given
        $article = new Article();

        // Then
        $this->assertInstanceOf(\DateTimeImmutable::class, $article->getCreatedAt(), 'createdAt doit être automatiquement initialisé lors de la création');
        $this->assertFalse($article->isPublished(), 'Les articles doivent être en brouillon par défaut');
    }

    public function testArticleCreatedAtIsSetToCurrentTime(): void
    {
        // Given
        $beforeCreation = new \DateTimeImmutable();

        // When
        $article = new Article();

        // Then
        $afterCreation = new \DateTimeImmutable();
        $createdAt = $article->getCreatedAt();

        $this->assertGreaterThanOrEqual($beforeCreation, $createdAt);
        $this->assertLessThanOrEqual($afterCreation, $createdAt);
    }

    public function testUpdateTimestampCallbackSetsUpdateAt(): void
    {
        // Given
        $article = new Article();
        $this->assertNull($article->getUpdateAt(), 'updateAt doit être null initialement');

        // When
        $article->updateTimestamp();

        // Then
        $this->assertInstanceOf(\DateTime::class, $article->getUpdateAt(), 'updateAt doit être défini après l\'appel du callback');
    }

    public function testCompleteArticleCreationWorkflow(): void
    {
        // Given
        $article = new Article();

        // When - Configuration complète de l'article
        $article->setTitle('Article de test');
        $article->setContent('Contenu de test pour l\'article');
        $article->setSlug('article-de-test');
        $article->setImageUrl('https://example.com/image.jpg');
        $article->setCategory(CategoryEnum::ASSEMBLEE);
        $article->setIsPublished(true);

        // Then
        $this->assertEquals('Article de test', $article->getTitle());
        $this->assertEquals('Contenu de test pour l\'article', $article->getContent());
        $this->assertEquals('article-de-test', $article->getSlug());
        $this->assertEquals('https://example.com/image.jpg', $article->getImageUrl());
        $this->assertEquals(CategoryEnum::ASSEMBLEE, $article->getCategory());
        $this->assertTrue($article->isPublished());
        $this->assertInstanceOf(\DateTimeImmutable::class, $article->getCreatedAt());
    }

    public function testMultipleUpdateTimestampCallsUpdateTheTime(): void
    {
        // Given
        $article = new Article();

        // When
        $article->updateTimestamp();
        $firstUpdate = $article->getUpdateAt();

        // Attendre un moment pour s'assurer que les timestamps sont différents
        usleep(1000); // 1ms

        $article->updateTimestamp();
        $secondUpdate = $article->getUpdateAt();

        // Then
        $this->assertNotEquals($firstUpdate, $secondUpdate, 'Les appels successifs à updateTimestamp() doivent mettre à jour le timestamp');
        $this->assertGreaterThan($firstUpdate, $secondUpdate);
    }
}
