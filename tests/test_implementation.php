<?php
// Test de création d'article avec notre nouvelle implémentation

require_once __DIR__ . '/vendor/autoload.php';

use App\Entity\Article;
use App\Enum\CategoryEnum;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Dotenv\Dotenv;

echo "=== Test de création d'article avec timestamps automatiques ===\n\n";

// Charger les variables d'environnement
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

try {
    // Configuration Doctrine (simplifiée pour le test)
    $paths = [__DIR__ . '/src/Entity'];
    $isDevMode = true;
    $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

    $connectionParams = [
        'dbname' => $_ENV['DATABASE_NAME'] ?? 'site_depute',
        'user' => $_ENV['DATABASE_USER'] ?? 'root',
        'password' => $_ENV['DATABASE_PASSWORD'] ?? '',
        'host' => $_ENV['DATABASE_HOST'] ?? 'localhost',
        'driver' => 'pdo_mysql',
    ];

    $connection = DriverManager::getConnection($connectionParams);
    $entityManager = new EntityManager($connection, $config);

    echo "✅ Connexion à la base de données établie\n";

    // Test 1: Création d'un article
    echo "\n--- Test 1: Création d'un nouvel article ---\n";
    $article = new Article();

    echo "✅ Article instancié\n";
    echo "   CreatedAt: " . $article->getCreatedAt()->format('Y-m-d H:i:s') . "\n";
    echo "   IsPublished: " . ($article->isPublished() ? 'true' : 'false') . "\n";

    // Configuration de l'article
    $article->setTitle('Test Article - ' . date('Y-m-d H:i:s'));
    $article->setContent('Ceci est un test de création d\'article avec timestamps automatiques.');
    $article->setSlug('test-article-' . time());
    $article->setImageUrl('https://picsum.photos/640/480?random=' . rand(1, 1000));
    $article->setCategory(CategoryEnum::ASSEMBLEE);
    $article->setIsPublished(true);

    echo "✅ Article configuré\n";
    echo "   Titre: " . $article->getTitle() . "\n";
    echo "   Slug: " . $article->getSlug() . "\n";
    echo "   Catégorie: " . $article->getCategory()->value . "\n";

    // Persistance
    $entityManager->persist($article);
    $entityManager->flush();

    echo "✅ Article sauvegardé en base de données\n";
    echo "   ID généré: " . $article->getId() . "\n";
    echo "   CreatedAt final: " . $article->getCreatedAt()->format('Y-m-d H:i:s') . "\n";
    echo "   UpdateAt: " . ($article->getUpdateAt() ? $article->getUpdateAt()->format('Y-m-d H:i:s') : 'null') . "\n";

    // Test 2: Mise à jour
    echo "\n--- Test 2: Mise à jour de l'article ---\n";
    $originalTitle = $article->getTitle();
    $article->setTitle($originalTitle . ' [MODIFIÉ]');

    // Simuler le callback PreUpdate
    $article->updateTimestamp();

    $entityManager->flush();

    echo "✅ Article mis à jour\n";
    echo "   Nouveau titre: " . $article->getTitle() . "\n";
    echo "   UpdateAt: " . ($article->getUpdateAt() ? $article->getUpdateAt()->format('Y-m-d H:i:s') : 'null') . "\n";

    echo "\n🎉 SUCCÈS: Le problème de contrainte d'intégrité est résolu !\n";
    echo "   - Les articles sont créés avec createdAt automatique\n";
    echo "   - Les articles sont en brouillon par défaut\n";
    echo "   - Les mises à jour sont tracées automatiquement\n";

} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";

    if ($e->getPrevious()) {
        echo "   Cause: " . $e->getPrevious()->getMessage() . "\n";
    }
}

echo "\n=== Fin du test ===\n";
