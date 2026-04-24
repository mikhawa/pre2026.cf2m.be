<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Partenaire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Redimensionne le logo d'un partenaire après upload (max 400×300 px, ratio conservé).
 */
class PartenaireLogoResizeSubscriber implements EventSubscriberInterface
{
    private const MAX_WIDTH = 400;
    private const MAX_HEIGHT = 300;

    public function __construct(private readonly StorageInterface $storage) {}

    public static function getSubscribedEvents(): array
    {
        return [Events::POST_UPLOAD => 'onPostUpload'];
    }

    public function onPostUpload(Event $event): void
    {
        $partenaire = $event->getObject();

        if (!$partenaire instanceof Partenaire) {
            return;
        }

        $filepath = $this->storage->resolvePath($partenaire, 'logoFile');

        if ($filepath === null || !file_exists($filepath)) {
            return;
        }

        $info = @getimagesize($filepath);
        if ($info === false) {
            return;
        }

        [$srcWidth, $srcHeight, $type] = $info;

        if ($srcWidth <= self::MAX_WIDTH && $srcHeight <= self::MAX_HEIGHT) {
            return;
        }

        $ratio = min(self::MAX_WIDTH / $srcWidth, self::MAX_HEIGHT / $srcHeight);
        $destWidth = (int) round($srcWidth * $ratio);
        $destHeight = (int) round($srcHeight * $ratio);

        $src = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($filepath),
            IMAGETYPE_PNG  => imagecreatefrompng($filepath),
            IMAGETYPE_GIF  => imagecreatefromgif($filepath),
            IMAGETYPE_WEBP => imagecreatefromwebp($filepath),
            default        => null,
        };

        if ($src === null || $src === false) {
            return;
        }

        $dest = imagecreatetruecolor($destWidth, $destHeight);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            $transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
            imagefilledrectangle($dest, 0, 0, $destWidth, $destHeight, $transparent);
        }

        imagecopyresampled($dest, $src, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);

        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($dest, $filepath, 85),
            IMAGETYPE_PNG  => imagepng($dest, $filepath),
            IMAGETYPE_GIF  => imagegif($dest, $filepath),
            IMAGETYPE_WEBP => imagewebp($dest, $filepath, 85),
            default        => null,
        };

        imagedestroy($src);
        imagedestroy($dest);
    }
}
