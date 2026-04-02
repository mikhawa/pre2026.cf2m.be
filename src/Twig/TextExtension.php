<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Fournit le filtre Twig `plain_text` pour convertir du HTML riche
 * (contenu SunEditor potentiellement encodé en entités) en texte brut.
 */
class TextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('plain_text', $this->plainText(...)),
        ];
    }

    /**
     * Décode les entités HTML puis supprime toutes les balises.
     * Exemple : "&lt;h2&gt;Titre&lt;/h2&gt;" → "Titre"
     */
    public function plainText(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        // Décode les entités encodées (&lt;h2&gt; → <h2>)
        $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Supprime toutes les balises HTML
        $stripped = strip_tags($decoded);

        // Normalise les espaces multiples et sauts de ligne
        return trim(preg_replace('/\s+/', ' ', $stripped) ?? '');
    }
}
