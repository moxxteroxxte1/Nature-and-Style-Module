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
        //Admin Controller
        \OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class           => \NatureAndStyle\CoreModule\Application\Controller\Admin\ActionsMain::class,
        \OxidEsales\Eshop\Application\Controller\Admin\ActionsList::class           => \NatureAndStyle\CoreModule\Application\Controller\Admin\ActionsList::class,
        \OxidEsales\Eshop\Application\Controller\Admin\DeliveryMain::class          => \NatureAndStyle\CoreModule\Application\Controller\Admin\DeliveryMain::class,
        \OxidEsales\Eshop\Application\Controller\Admin\ArticleStock::class          => \NatureAndStyle\CoreModule\Application\Controller\Admin\ArticleStock::class,

        //Controller
        \OxidEsales\Eshop\Application\Controller\StartController::class             => \NatureAndStyle\CoreModule\Application\Controller\StartController::class,
        \OxidEsales\Eshop\Application\Controller\BasketController::class            => \NatureAndStyle\CoreModule\Application\Controller\BasketController::class,

        //Components
        \OxidEsales\Eshop\Application\Component\UserComponent::class                => \NatureAndStyle\CoreModule\Application\Component\UserComponent::class,

        //Models
        \OxidEsales\Eshop\Application\Model\User::class                             => \NatureAndStyle\CoreModule\Application\Model\User::class,
        \OxidEsales\Eshop\Application\Model\OrderArticle::class                     => \NatureAndStyle\CoreModule\Application\Model\OrderArticle::class,
        \OxidEsales\Eshop\Application\Model\Article::class                          => \NatureAndStyle\CoreModule\Application\Model\Article::class,
        \OxidEsales\Eshop\Application\Model\Discount::class                         => \NatureAndStyle\CoreModule\Application\Model\Discount::class,
        \OxidEsales\Eshop\Application\Model\Actions::class                          => \NatureAndStyle\CoreModule\Application\Model\Actions::class,
        \OxidEsales\Eshop\Application\Model\ActionList::class                       => \NatureAndStyle\CoreModule\Application\Model\ActionList::class,
        \OxidEsales\Eshop\Application\Model\Delivery::class                         => \NatureAndStyle\CoreModule\Application\Model\Delivery::class,
        \OxidEsales\Eshop\Application\Model\Basket::class                           => \NatureAndStyle\CoreModule\Application\Model\Basket::class,
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
        'tile'                  => \NatureAndStyle\CoreModule\Application\Controller\Admin\TileController::class,
        'tile_list'             => \NatureAndStyle\CoreModule\Application\Controller\Admin\TileList::class,
        'tile_main'             => \NatureAndStyle\CoreModule\Application\Controller\Admin\TileMain::class,
    ),
    'events'        => array(
        'onActivate'   => '\NatureAndStyle\CoreModule\Core\Events::onActivate',
    ),
    'blocks'        => array(
        //Actions
        array(
            'template'  => 'actions_main.tpl',
            'block'     => 'admin_actions_main_product',
            'file'      => '/Application/views/blocks/actions_main_extend.tpl'
        ),

        //Article
        array(
            'template'  => 'article_main.tpl',
            'block'     => 'admin_article_main_extended',
            'file'      => '/Application/views/blocks/article_main_extend.tpl',
        ),
        array(
            'template'  => 'article_extend.tpl',
            'block'     => 'admin_article_extend_form',
            'file'      => 'Application/views/blocks/article_extend_extend.tpl',
        ),
        array(
            'template'  => 'article_stock.tpl',
            'block'     => 'admin_article_stock_form',
            'file'      => '/Application/views/blocks/article_stock_extend.tpl'
        ),
        array(
            'template'  => 'article_pictures.tpl',
            'block'     => 'admin_article_pictures_main',
            'file'      => '/Application/views/blocks/article_pictures_extend.tpl',
        ),
        //Delivery
        array(
            'template'  => 'delivery_main.tpl',
            'block'     => 'admin_delivery_main_form',
            'file'      => '/Application/views/blocks/delivery_main_extend.tpl',
        ),

        //Discount
        array(
            'template'  => 'discount_main.tpl',
            'block'     => 'admin_discount_main_form',
            'file'      => '/Application/views/blocks/discount_main_extend.tpl',
        ),

        //User
        array(
            'template'  => 'user_main.tpl',
            'block'     => 'admin_user_main_assign_groups',
            'file'      => '/Application/views/blocks/user_main_extend.tpl',
        ),
    ),
);