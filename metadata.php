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
        \OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class            => \NatureAndStyle\CoreModule\Application\Controller\Admin\ActionsMain::class,
        \OxidEsales\Eshop\Application\Component\UserComponent::class                => \NatureAndStyle\CoreModule\Application\Component\UserComponent::class,
        \OxidEsales\Eshop\Application\Model\User::class                             => \NatureAndStyle\CoreModule\Application\Model\User::class,
        \OxidEsales\Eshop\Application\Model\Article::class                          => \NatureAndStyle\CoreModule\Application\Model\Article::class,
        \OxidEsales\Eshop\Application\Model\Discount::class                         => \NatureAndStyle\CoreModule\Application\Model\Discount::class,
        \OxidEsales\Eshop\Application\Model\Actions::class                          => \NatureAndStyle\CoreModule\Application\Model\Actions::class,
        \OxidEsales\Eshop\Core\Price::class                                         => \NatureAndStyle\CoreModule\Core\Price::class,
    ),
    'templates'     => array(
        'actions_category.tpl'  => 'nature-and-style/core-module/Application/views/admin/tpl/popups/actions_category.tpl',
        'tile_category.tpl'     => 'nature-and-style/core-module/Application/views/admin/tpl/popups/tile_category.tpl',
        'tile.tpl'              => 'nature-and-style/core-module/Application/views/admin/tpl/tile.tpl',
        'tile_list.tpl'         => 'nature-and-style/core-module/Application/views/admin/tpl/tile_list.tpl',
        'tile_main.tpl'         => 'nature-and-style/core-module/Application/views/admin/tpl/tile_main.tpl',
    ),
    'controllers'   => array(
        'actions_category_ajax' => \NatureAndStyle\CoreModule\Application\Controller\Admin\ActionsCategoryAjax::class,
        'tile_category_ajax'    => \NatureAndStyle\CoreModule\Application\Controller\Admin\TileCategoryAjax::class,
        'tilecontroller'        => \NatureAndStyle\CoreModule\Application\Controller\Admin\TileController::class,
        'tile_list'             => \NatureAndStyle\CoreModule\Application\Controller\Admin\TileList::class,
        'tile_main'             => \NatureAndStyle\CoreModule\Application\Controller\Admin\TileMain::class,
    ),
    'events'        => array(
        'onActivate'   => '\NatureAndStyle\CoreModule\Core\Events::onActivate',
    ),
    'blocks'        => array(
        array(
            'template'  => 'article_pictures.tpl',
            'block'     => 'admin_article_pictures_main',
            'file'      => '/Application/views/blocks/article_pictures_extend.tpl',
        ),
        array(
            'template'  => 'user_main.tpl',
            'block'     => 'admin_user_main_assign_groups',
            'file'      => '/Application/views/blocks/user_main_extend.tpl',
        ),
        array(
            'template'  => 'article_main.tpl',
            'block'     => 'admin_article_main_extended',
            'file'      => '/Application/views/blocks/article_main_extend.tpl',
        ),
        array(
            'template'  => 'discount_main.tpl',
            'block'     => 'admin_discount_main_form',
            'file'      => '/Application/views/blocks/discount_main_extend.tpl',
        ),
        array(
            'template'  => 'actions_main.tpl',
            'block'     => 'admin_actions_main_product',
            'file'      => '/Application/views/blocks/actions_main_extend.tpl'
        )
    ),
);