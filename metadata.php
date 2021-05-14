<?php

$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'            => 'nascore',
    'title'         => [
        'de'    => 'Nature and Style.',
        'en'    => 'Nature and Style.',
    ],
    'description'   => [
        'de'    => 'Nature and Style Erweiterung.',
        'en'    => 'Nature and Style extension.',
    ],
    'thumbnail'     => 'out/pictures/logo.png',
    'version'       => '1.0.0',
    'author'        => 'Nature and Style',
    'url'           => 'http://www.natureandstyle.com/',
    'email'         => 'info@natureandstyle.de',
    'extend'        => array(
        \OxidEsales\GdprOptinModule\Component\UserComponent::class                => \NatureAndStyle\CoreModule\Application\Component\UserComponent::class,
        \OxidEsales\Eshop\Application\Model\User::class                             => \NatureAndStyle\CoreModule\Application\Model\User::class,
        \OxidEsales\Eshop\Application\Model\Basket::class                           => \NatureAndStyle\CoreModule\Application\Model\Basket::class,
        \OxidEsales\Eshop\Core\Price::class                                         => \NatureAndStyle\CoreModule\Core\Price::class,
    ),
    'events'       => array(
        'onActivate'   => '\NatureAndStyle\CoreModule\Core\Events::onActivate',
    ),
    'blocks'        => array(
        array(
            'template'  => 'article_pictures.tpl',
            'block'     => 'admin_article_pictures_main',
            'file'      => '/views/blocks/article_pictures_extend.tpl',
        ),
        array(
            'template'  => 'user_main.tpl',
            'block'     => 'admin_user_main_assign_groups',
            'file'      => '/views/blocks/user_main.tpl',
        ),
    ),
);