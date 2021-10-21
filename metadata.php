<?php

use NatureAndStyle\CoreModule\Application\Component\UserComponent;
use NatureAndStyle\CoreModule\Application\Component\Widget\ArticleDetails;
use NatureAndStyle\CoreModule\Application\Controller\Admin\ActionsCategoryAjax;
use NatureAndStyle\CoreModule\Application\Controller\Admin\ActionsList;
use NatureAndStyle\CoreModule\Application\Controller\Admin\ActionsMain;
use NatureAndStyle\CoreModule\Application\Controller\Admin\ArticlePictures;
use NatureAndStyle\CoreModule\Application\Controller\Admin\ArticleStock;
use NatureAndStyle\CoreModule\Application\Controller\Admin\ContentMain;
use NatureAndStyle\CoreModule\Application\Controller\Admin\DeliveryMain;
use NatureAndStyle\CoreModule\Application\Controller\Admin\GenericExportDo;
use NatureAndStyle\CoreModule\Application\Controller\Admin\DynamicExportBaseController;
use NatureAndStyle\CoreModule\Application\Controller\Admin\GenericImportMain;
use NatureAndStyle\CoreModule\Application\Controller\Admin\ShopConfiguration;
use NatureAndStyle\CoreModule\Application\Controller\Admin\TileController;
use NatureAndStyle\CoreModule\Application\Controller\Admin\TileList;
use NatureAndStyle\CoreModule\Application\Controller\Admin\TileMain;
use NatureAndStyle\CoreModule\Application\Controller\BasketController;
use NatureAndStyle\CoreModule\Application\Controller\PaymentController;
use NatureAndStyle\CoreModule\Application\Controller\StartController;
use NatureAndStyle\CoreModule\Application\Model\ActionList;
use NatureAndStyle\CoreModule\Application\Model\Actions;
use NatureAndStyle\CoreModule\Application\Model\Article;
use NatureAndStyle\CoreModule\Application\Model\Basket;
use NatureAndStyle\CoreModule\Application\Model\BasketItem;
use NatureAndStyle\CoreModule\Application\Model\Content;
use NatureAndStyle\CoreModule\Application\Model\ContentList;
use NatureAndStyle\CoreModule\Application\Model\Delivery;
use NatureAndStyle\CoreModule\Application\Model\DeliveryList;
use NatureAndStyle\CoreModule\Application\Model\Discount;
use NatureAndStyle\CoreModule\Application\Model\OrderArticle;
use NatureAndStyle\CoreModule\Application\Model\User;
use NatureAndStyle\CoreModule\Core\Base;
use NatureAndStyle\CoreModule\Core\GenericImport\GenericImport;
use NatureAndStyle\CoreModule\Core\Price;
use NatureAndStyle\CoreModule\Core\PriceList;

$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id' => 'nascore',
    'title' => [
        'de' => 'Nature and Style.',
        'en' => 'Nature and Style.',
    ],
    'description' => [
        'de' => 'Nature and Style Erweiterung.',
        'en' => 'Nature and Style extension.',
    ],
    'thumbnail' => 'out/pictures/logo.png',
    'version' => '1.0.0',
    'author' => 'Nature and Style',
    'url' => 'https://www.natureandstyle.com/',
    'email' => 'info@natureandstyle.de',
    'extend' => array(
        //Core
        \OxidEsales\Eshop\Core\Price::class => Price::class,
        \OxidEsales\Eshop\Core\GenericImport\ImportObject\Article::class => \NatureAndStyle\CoreModule\Core\GenericImport\ImportObject\Article::class,
        \OxidEsales\Eshop\Core\GenericImport\GenericImport::class => GenericImport::class,

        //Admin Controller
        \OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class => ActionsMain::class,
        \OxidEsales\Eshop\Application\Controller\Admin\ActionsList::class => ActionsList::class,
        \OxidEsales\Eshop\Application\Controller\Admin\DeliveryMain::class => DeliveryMain::class,
        \OxidEsales\Eshop\Application\Controller\Admin\ArticleStock::class => ArticleStock::class,
        \OxidEsales\Eshop\Application\Controller\Admin\ArticlePictures::class => ArticlePictures::class,
        \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration::class => ShopConfiguration::class,
        \OxidEsales\Eshop\Application\Controller\Admin\DynamicExportBaseController::class => DynamicExportBaseController::class,
        \OxidEsales\Eshop\Application\Controller\Admin\ContentMain::class => ContentMain::class,
        \OxidEsales\Eshop\Application\Controller\Admin\GenericExportDo::class => GenericExportDo::class,
        \OxidEsales\Eshop\Application\Controller\Admin\GenericImportMain::class => GenericImportMain::class,

        //Controller
        \OxidEsales\Eshop\Application\Controller\StartController::class => StartController::class,
        \OxidEsales\Eshop\Application\Controller\BasketController::class => BasketController::class,
        \OxidEsales\Eshop\Application\Controller\PaymentController::class => PaymentController::class,

        //Components
        \OxidEsales\Eshop\Application\Component\UserComponent::class => UserComponent::class,
        \OxidEsales\Eshop\Application\Component\Widget\ArticleDetails::class => ArticleDetails::class,

        //Models
        \OxidEsales\Eshop\Application\Model\User::class => User::class,
        \OxidEsales\Eshop\Application\Model\OrderArticle::class => OrderArticle::class,
        \OxidEsales\Eshop\Application\Model\Article::class => Article::class,
        \OxidEsales\Eshop\Application\Model\Discount::class => Discount::class,
        \OxidEsales\Eshop\Application\Model\Actions::class => Actions::class,
        \OxidEsales\Eshop\Application\Model\ActionList::class => ActionList::class,
        \OxidEsales\Eshop\Application\Model\Delivery::class => Delivery::class,
        \OxidEsales\Eshop\Application\Model\DeliveryList::class => DeliveryList::class,
        \OxidEsales\Eshop\Application\Model\Basket::class => Basket::class,
        \OxidEsales\Eshop\Application\Model\BasketItem::class => BasketItem::class,
        \OxidEsales\Eshop\Application\Model\Content::class => Content::class,
        \OxidEsales\Eshop\Application\Model\ContentList::class => ContentList::class,

    ),
    'templates' => array(
        'actions_category.tpl' => 'nature-and-style/core-module/Application/views/admin/tpl/popups/actions_category.tpl',
        'tile_category.tpl' => 'nature-and-style/core-module/Application/views/admin/tpl/popups/tile_category.tpl',
        'tile.tpl' => 'nature-and-style/core-module/Application/views/admin/tpl/tile.tpl',
        'tile_list.tpl' => 'nature-and-style/core-module/Application/views/admin/tpl/tile_list.tpl',
        'tile_main.tpl' => 'nature-and-style/core-module/Application/views/admin/tpl/tile_main.tpl',
        'genexport.tpl' => 'nature-and-style/core-module/Application/views/admin/tpl/genexport.tpl',
    ),
    'controllers' => array(
        'actions_category_ajax' => ActionsCategoryAjax::class,
        'tile' => TileController::class,
        'tile_list' => TileList::class,
        'tile_main' => TileMain::class
    ),
    'events' => array(
        'onActivate' => '\NatureAndStyle\CoreModule\Core\Events::onActivate',
    ),
    'blocks' => array(
        //Actions
        array(
            'template' => 'actions_main.tpl',
            'block' => 'admin_actions_main_product',
            'file' => '/Application/views/blocks/actions_main_extend.tpl'
        ),
        array(
            'template' => 'actions_main.tpl',
            'block' => 'admin_actions_main_form',
            'file' => '/Application/views/blocks/actions_main_extended_extend.tpl'
        ),

        //Article
        array(
            'template' => 'article_main.tpl',
            'block' => 'admin_article_main_extended',
            'file' => '/Application/views/blocks/article_main_extend.tpl',
        ),
        array(
            'template' => 'article_extend.tpl',
            'block' => 'admin_article_extend_form',
            'file' => 'Application/views/blocks/article_extend_extend.tpl',
        ),
        array(
            'template' => 'article_stock.tpl',
            'block' => 'admin_article_stock_form',
            'file' => '/Application/views/blocks/article_stock_extend.tpl'
        ),
        array(
            'template' => 'article_pictures.tpl',
            'block' => 'admin_article_pictures_main',
            'file' => '/Application/views/blocks/article_pictures_extend.tpl',
        ),
        //CONTENT
        array(
            'template' => 'content_main.tpl',
            'block' => 'admin_content_main_form',
            'file' => '/Application/views/blocks/content_main_extend.tpl',
        ),
        //Delivery
        array(
            'template' => 'delivery_main.tpl',
            'block' => 'admin_delivery_main_form',
            'file' => '/Application/views/blocks/delivery_main_extend.tpl',
        ),

        //Discount
        array(
            'template' => 'discount_main.tpl',
            'block' => 'admin_discount_main_form',
            'file' => '/Application/views/blocks/discount_main_extend.tpl',
        ),

        //User
        array(
            'template' => 'user_main.tpl',
            'block' => 'admin_user_main_form',
            'file' => '/Application/views/blocks/user_main_extend.tpl',
        ),
        array(
            'template' => 'user_address.tpl',
            'block' => 'admin_user_address_form',
            'file' => '/Application/views/blocks/user_address_extend.tpl',
        ),

        //Shop Config
        array(
            'template' => 'shop_config.tpl',
            'block' => 'admin_shop_config_options',
            'file' => '/Application/views/blocks/shop_config_extend.tpl',
        ),
    ),
);