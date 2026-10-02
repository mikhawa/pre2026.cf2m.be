<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'admin' => [
        'path' => './assets/admin.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    'suneditor' => [
        'version' => '2.47.8',
    ],
    'bootstrap' => [
        'version' => '5.3.8',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.8',
        'type' => 'css',
    ],
    'suneditor/dist/css/suneditor.min.css' => [
        'version' => '2.47.8',
        'type' => 'css',
    ],
    'suneditor/src/plugins' => [
        'version' => '2.47.8',
    ],
    'cropperjs' => [
        'version' => '2.1.0',
    ],
    '@cropper/utils' => [
        'version' => '2.1.0',
    ],
    '@cropper/elements' => [
        'version' => '2.1.0',
    ],
    '@cropper/element' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-canvas' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-image' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-shade' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-handle' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-selection' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-grid' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-crosshair' => [
        'version' => '2.1.0',
    ],
    '@cropper/element-viewer' => [
        'version' => '2.1.0',
    ],
    '@symfony/ux-react' => [
        'path' => './vendor/symfony/ux-react/assets/dist/loader.js',
    ],
    // React : entrées ajoutées à la main (bug d'AssetMapper 7.4.19 : `importmap:require`
    // échoue sur les dépendances encodées « react@%5E19.3.0 » renvoyées par jsDelivr).
    // Toujours garder les versions react / react-dom identiques.
    'react' => [
        'version' => '19.3.0',
    ],
    'react/jsx-runtime' => [
        'version' => '19.3.0',
    ],
    'react-dom' => [
        'version' => '19.3.0',
    ],
    'react-dom/client' => [
        'version' => '19.3.0',
    ],
    'scheduler' => [
        'version' => '0.28.0',
    ],
];
