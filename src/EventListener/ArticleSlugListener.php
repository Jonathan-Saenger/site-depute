<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\SlugGenerator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
final class ArticleSlugListener
{
    public function __construct(
        private SlugGenerator $slugGenerator,
        private ArticleRepository $articleRepository
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->generateSlug($args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Article) {
            return;
        }

        // Générer un nouveau slug si le titre a changé ou si le slug est vide
        if ($args->hasChangedField('title') || empty($entity->getSlug())) {
            $this->generateSlug($entity);
        }
    }

    private function generateSlug(object $entity): void
    {
        if (!$entity instanceof Article) {
            return;
        }

        $title = $entity->getTitle();
        if (empty($title)) {
            return;
        }

        // Si l'article a déjà un slug et que le titre n'a pas changé, on garde le slug existant
        if (!empty($entity->getSlug())) {
            return;
        }

        // Fonction de vérification d'unicité
        $existsCallback = function (string $slug) use ($entity): bool {
            $existingArticle = $this->articleRepository->findOneBy(['slug' => $slug]);

            // Si aucun article trouvé avec ce slug, il est disponible
            if (!$existingArticle) {
                return false;
            }

            // Si l'article trouvé est le même que celui qu'on modifie, le slug est disponible
            return $existingArticle->getId() !== $entity->getId();
        };

        $slug = $this->slugGenerator->generateUnique($title, $existsCallback);
        $entity->setSlug($slug);
    }
}
