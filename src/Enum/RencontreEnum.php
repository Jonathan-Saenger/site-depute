<?php

declare(strict_types=1);

namespace App\Enum;

enum RencontreEnum: string
{
    case CITOYENNE = 'citoyennes';
    case PUBLIQUE = 'publiques';
    case DEPLACEMENT = 'deplacements';

    public function getLabel(): string
    {
        return match ($this) {
            self::CITOYENNE => 'Rencontre citoyenne',
            self::PUBLIQUE => 'Réunion publique',
            self::DEPLACEMENT => 'Déplacement',
        };
    }
}