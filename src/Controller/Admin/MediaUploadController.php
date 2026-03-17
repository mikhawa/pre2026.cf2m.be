<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Gestion des uploads d'images depuis SunEditor dans EasyAdmin.
 */
#[Route('/admin/media', name: 'admin_media_')]
#[IsGranted('ROLE_FORMATEUR')]
class MediaUploadController extends AbstractController
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly string $uploadsEditorDir,
        private readonly string $uploadsEditorUrl,
    ) {
    }

    /**
     * Point d'entrée d'upload pour SunEditor.
     * Retourne le format JSON attendu par SunEditor :
     * { "result": [{ "url": "...", "name": "...", "size": 123 }] }
     */
    #[Route('/upload', name: 'upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        // SunEditor envoie les fichiers sous les clés "file-0", "file-1", etc.
        $files = [];
        foreach ($request->files->all() as $key => $uploadedFile) {
            if (str_starts_with((string) $key, 'file')) {
                $files[] = $uploadedFile;
            }
        }

        if (empty($files)) {
            return $this->json(['errorMessage' => 'Aucun fichier reçu.'], Response::HTTP_BAD_REQUEST);
        }

        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        ];
        $maxSizeBytes = 5 * 1024 * 1024; // 5 Mo

        $results = [];

        foreach ($files as $file) {
            // Validation MIME
            if (!in_array($file->getMimeType(), $allowedMimeTypes, true)) {
                return $this->json([
                    'errorMessage' => sprintf(
                        'Type de fichier non autorisé : %s. Types acceptés : JPEG, PNG, GIF, WEBP, SVG.',
                        $file->getMimeType()
                    ),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Validation taille
            if ($file->getSize() > $maxSizeBytes) {
                return $this->json([
                    'errorMessage' => sprintf(
                        'Fichier trop volumineux (%s Mo). Maximum : 5 Mo.',
                        number_format($file->getSize() / 1024 / 1024, 2)
                    ),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Infos à conserver avant déplacement (getSize() invalide après move)
            $originalName  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $originalFull  = $file->getClientOriginalName();
            $fileSize      = $file->getSize();

            $safeName      = $this->slugger->slug($originalName)->lower();
            $extension     = strtolower($file->guessExtension() ?? $file->getClientOriginalExtension());
            $uniqueName    = $safeName . '-' . uniqid() . '.' . $extension;

            // Déplacement vers le répertoire d'upload
            $file->move($this->uploadsEditorDir, $uniqueName);

            $results[] = [
                'url'  => $this->uploadsEditorUrl . '/' . $uniqueName,
                'name' => $originalFull,
                'size' => $fileSize,
            ];
        }

        return $this->json(['result' => $results]);
    }
}
