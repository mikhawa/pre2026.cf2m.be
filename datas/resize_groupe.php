<?php
$src = '/var/www/html/datas/20250513_151917.jpg';
$dst = '/var/www/html/public/images/hero-groupe.jpg';

$img = imagecreatefromjpeg($src);
$w = imagesx($img);
$h = imagesy($img);

$new_w = 800;
$new_h = (int)($h * $new_w / $w);

$resized = imagecreatetruecolor($new_w, $new_h);
imagecopyresampled($resized, $img, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
imagejpeg($resized, $dst, 85);

echo $new_w . 'x' . $new_h . ' — ' . round(filesize($dst)/1024) . ' Ko' . PHP_EOL;

imagedestroy($img);
imagedestroy($resized);
