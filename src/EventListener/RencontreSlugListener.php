<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Rencontre;
use App\Repository\RencontreRepository;
use App\Service\SlugGenerator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
final class RencontreSlugListener
{
    public function __construct(
        private SlugGenerator $slugGenerator,
        private RencontreRepository $rencontreRepository
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->generateSlug($args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Rencontre) {
            return;
        }

        // Générer un nouveau slug si le titre a changé ou si le slug est vide
        if ($args->hasChangedField('titre') || empty($entity->getSlug())) {
            $this->generateSlug($entity);
        }
    }

    private function generateSlug(object $entity): void
    {
        if (!$entity instanceof Rencontre) {
            return;
        }

        $titre = $entity->getTitre();
        if (empty($titre)) {
            return;
        }

        // Si la rencontre a déjà un slug et que le titre n'a pas changé, on garde le slug existant
        if (!empty($entity->getSlug())) {
            return;
        }

        // Fonction de vérification d'unicité
        $existsCallback = function (string $slug) use ($entity): bool {
            $existingRencontre = $this->rencontreRepository->findOneBy(['slug' => $slug]);

            // Si aucune rencontre trouvée avec ce slug, il est disponible
            if (!$existingRencontre) {
                return false;
            }

            // Si la rencontre trouvée est la même que celle qu'on modifie, le slug est disponible
            return $existingRencontre->getId() !== $entity->getId();
        };

        $slug = $this->slugGenerator->generateUnique($titre, $existsCallback);
        $entity->setSlug($slug);
    }
}