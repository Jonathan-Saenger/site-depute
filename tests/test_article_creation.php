<?php

// Script de test pour vérifier la création automatique des timestamps
require_once __DIR__ . '/vendor/autoload.php';

use App\Entity\Article;
use App\Enum\CategoryEnum;

echo "=== Test de création d'un article ===\n";

// Création d'un nouvel article
$article = new Article();

echo "Article créé avec succès!\n";
echo "ID: " . ($article->getId() ?? 'null') . "\n";
echo "CreatedAt: " . ($article->getCreatedAt() ? $article->getCreatedAt()->format('Y-m-d H:i:s') : 'null') . "\n";
echo "IsPublished: " . ($article->isPublished() ? 'true' : 'false') . "\n";

// Test des setters
$article->setTitle('Test Article');
$article->setContent('Contenu de test');
$article->setSlug('test-article');
$article->setImageUrl('https://example.com/image.jpg');
$article->setCategory(CategoryEnum::ASSEMBLEE);

echo "\nAprès configuration:\n";
echo "Title: " . $article->getTitle() . "\n";
echo "Content: " . $article->getContent() . "\n";
echo "Slug: " . $article->getSlug() . "\n";
echo "ImageUrl: " . $article->getImageUrl() . "\n";
echo "Category: " . $article->getCategory()->value . "\n";
echo "CreatedAt: " . $article->getCreatedAt()->format('Y-m-d H:i:s') . "\n";
echo "IsPublished: " . ($article->isPublished() ? 'true' : 'false') . "\n";

// Test du callback PreUpdate
echo "\nTest du callback PreUpdate...\n";
$originalUpdateAt = $article->getUpdateAt();
echo "UpdateAt avant: " . ($originalUpdateAt ? $originalUpdateAt->format('Y-m-d H:i:s') : 'null') . "\n";

// Simulation d'un PreUpdate (normalement appelé par Doctrine)
$article->updateTimestamp();
echo "UpdateAt après updateTimestamp(): " . ($article->getUpdateAt() ? $article->getUpdateAt()->format('Y-m-d H:i:s') : 'null') . "\n";

echo "\n=== Test terminé avec succès! ===\n";
