<?php

namespace NatureAndStyle\CoreModule\Core;

use NatureAndStyle\CoreModule\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class defines what module does on Shop events.
 */
class Events
{

    public static function onActivate()
    {
        $oCategory = oxNew('oxcategory');
        if (!$oCategory->load('new_articles')) {
            $oCategory->assign(array(
                'oxid' => 'new_articles',
                'oxtitle' => 'Unsere Neuheiten',
                'oxactive' => 1,
                "oxparentid" => "oxrootid",
                'oxsort' => 0
            ));
            $oCategory->save();
        }
        $oArticle = oxNew(Article::class);
        $logger = Registry::getLogger();
        $logger->info("Test");
        $logger->info($oArticle->load('test123'));
        if(!$oArticle->load('test123')){
            $oArticle->assign(array(
                'oxid' => 'test123',
                'oxtitle' => 'TEST',
                'oxartnum' => 'test123',
                'oxlongdesc' => 'TEST TEST TEST'
            ));
        }
    }
}