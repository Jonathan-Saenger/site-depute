<?php

declare(strict_types=1);

namespace App\Enum;

enum RencontreEnum: string
{
    case CITOYENNE = 'citoyennes';
    case PUBLIQUE = 'publiques';
    case DEPLACEMENT = 'deplacements';

    /**
     * Retourne le libellé correspondant au type
     */
    public function getLabel(): string
    {
        return match($this) {
            self::CITOYENNE => 'Rencontre citoyenne',
            self::PUBLIQUE => 'Réunion publique',
            self::DEPLACEMENT => 'Déplacement',
        };
    }

    /**
     * Retourne tous les types sous forme de tableau associatif pour les formulaires
     */
    public static function getChoices(): array
    {
        $choices = [];
        foreach(self::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }
        return $choices;
    }

    /**
     * Retourne tous les types en tant qu'objets enum
     */
    public static function getAllTypes(): array
    {
        return self::cases();
    }
}