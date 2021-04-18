<?php

$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'nascore',
    'title'       => [
        'de' => 'Nature and Style.',
        'en' => 'Nature and Style.',
    ],
    'description' => [
        'de' => 'Nature and Style Erweiterung.',
        'en' => 'Nature and Style extension.',
    ],
    'thumbnail'   => 'out/pictures/logo.png',
    'version'     => '1.0.0',
    'author'      => 'Nature and Style',
    'url'         => 'http://www.natureandstyle.com/',
    'email'       => 'info@natureandstyle.de',
    'extend'      => [
        \OxidEsales\Eshop\Core\Price::class => \NatureAndStyle\CoreModule\Core\Price::class,
    ]
);