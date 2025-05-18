<?php

declare(strict_types=1);

namespace App\Enum;

enum RencontreEnum: string
{
    case CITOYENNE = 'Rencontre citoyenne';
    case PUBLIQUE = 'Réunion publique';
    case DEPLACEMENT = 'Déplacement';
    case ASSOCIATION = 'Evènement associatif';
    case PERMANENCE = 'Permanence parlementaire';
    case INAUGURATION = 'Inauguration';

    public function getLabel(): string
    {
        return match ($this) {
            self::CITOYENNE => 'Rencontre citoyenne',
            self::PUBLIQUE => 'Réunion publique',
            self::DEPLACEMENT => 'Déplacement',
            self::ASSOCIATION => 'Evènement associatif',
            self::PERMANENCE => 'Permanence parlementaire',
            self::INAUGURATION => 'Inauguration',
        };
    }
}