<?php

declare(strict_types=1);

namespace App\Service;

class SlugGenerator
{
    /**
     * Génère un slug à partir d'un texte
     */
    public function generate(string $text, int $maxLength = 255): string
    {
        // Conversion en minuscules
        $slug = mb_strtolower($text, 'UTF-8');

        // Remplacement des caractères accentués
        $slug = $this->removeAccents($slug);

        // Remplacement des espaces et caractères spéciaux par des tirets
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Suppression des tirets en début et fin
        $slug = trim($slug, '-');

        // Suppression des tirets multiples consécutifs
        $slug = preg_replace('/-+/', '-', $slug);

        // Limitation de la longueur
        if (strlen($slug) > $maxLength) {
            $slug = substr($slug, 0, $maxLength);
            // Éviter de couper au milieu d'un mot
            $lastDash = strrpos($slug, '-');
            if ($lastDash !== false && $lastDash > $maxLength * 0.8) {
                $slug = substr($slug, 0, $lastDash);
            }
        }

        // Suppression des tirets en fin après la troncature
        $slug = rtrim($slug, '-');

        return $slug ?: 'article';
    }

    /**
     * Supprime les accents et caractères spéciaux
     */
    private function removeAccents(string $text): string
    {
        $patterns = [
            '/[àáâãäå]/' => 'a',
            '/[èéêë]/' => 'e',
            '/[ìíîï]/' => 'i',
            '/[òóôõö]/' => 'o',
            '/[ùúûü]/' => 'u',
            '/[ýÿ]/' => 'y',
            '/[ñ]/' => 'n',
            '/[ç]/' => 'c',
            '/[æ]/' => 'ae',
            '/[œ]/' => 'oe',
            '/[ß]/' => 'ss',
        ];

        return preg_replace(array_keys($patterns), array_values($patterns), $text);
    }

    /**
     * Génère un slug unique en ajoutant un suffixe numérique si nécessaire
     */
    public function generateUnique(string $text, callable $existsCallback, int $maxLength = 255): string
    {
        $baseSlug = $this->generate($text, $maxLength - 10); // Réserver de l'espace pour le suffixe
        $slug = $baseSlug;
        $counter = 1;

        while ($existsCallback($slug)) {
            $suffix = '-' . $counter;
            $maxBaseLength = $maxLength - strlen($suffix);

            if (strlen($baseSlug) > $maxBaseLength) {
                $truncatedBase = substr($baseSlug, 0, $maxBaseLength);
                $truncatedBase = rtrim($truncatedBase, '-');
                $slug = $truncatedBase . $suffix;
            } else {
                $slug = $baseSlug . $suffix;
            }

            $counter++;

            // Protection contre les boucles infinies
            if ($counter > 1000) {
                $slug = $baseSlug . '-' . uniqid();
                break;
            }
        }

        return $slug;
    }
}
