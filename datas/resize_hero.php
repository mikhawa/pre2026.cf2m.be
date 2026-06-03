<?php

$src = '/var/www/html/datas/20250513_151917.jpg';
$dst_jpg = '/var/www/html/public/images/hero-portrait.jpg';
$dst_webp = '/var/www/html/public/images/hero-portrait.webp';

$img = imagecreatefromjpeg($src);
$w = imagesx($img);
$h = imagesy($img);

$new_w = 900;
$new_h = (int) ($h * $new_w / $w);

$resized = imagecreatetruecolor($new_w, $new_h);
imagecopyresampled($resized, $img, 0, 0, 0, 0, $new_w, $new_h, $w, $h);

imagejpeg($resized, $dst_jpg, 85);
echo 'JPG: '.$new_w.'x'.$new_h.' — '.round(filesize($dst_jpg) / 1024).' Ko'.PHP_EOL;

imagewebp($resized, $dst_webp, 82);
echo 'WebP: '.round(filesize($dst_webp) / 1024).' Ko'.PHP_EOL;

imagedestroy($img);
imagedestroy($resized);
