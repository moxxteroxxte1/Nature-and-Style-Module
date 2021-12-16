<?php

namespace NatureAndStyle\CoreModule\Core;

use Composer\Script\Event;
use NatureAndStyle\CoreModule\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class defines what module does on Shop events.
 */
class Events
{
    static $aCategories = [
        'new_articles' => array(
            'oxid' => 'new_articles',
            'oxtitle' => 'Unsere Neuheiten',
            'oxactive' => 1,
            "oxparentid" => "oxrootid",
            'oxsort' => 0)
    ];

    static $aContents = [
        'oxinactive' => array(
            'oxloadid' => 'oxinactive',
            'oxsnippet' => 1,
            'oxtype'    => 0,
            'oxactive' => 1,
            'oxtitle' => 'Warten auf Freigabe',
            'oxcontent' => '[{oxmultilang ident="USER_NOT_ACTIVATED"}]',
        ),
        'onStock' => array(
            'oxloadid' => 'onStock',
            'oxsnippet' => 1,
            'oxtype'    => 0,
            'oxactive' => 1,
            'oxtitle' => 'Auf Lager',
            'oxcontent' => '[{oxmultilang ident="READY_FOR_SHIPPING"}]',
        ),
        'lowStock' => array(
            'oxloadid' => 'lowStock',
            'oxsnippet' => 1,
            'oxtype'    => 0,
            'oxactive' => 1,
            'oxtitle' => 'Geringer Lagerbestand',
            'oxcontent' => '[{oxmultilang ident="LOW_STOCK"}]',
        ),
        'notOnStock' => array(
            'oxloadid' => 'notOnStock',
            'oxsnippet' => 1,
            'oxtype'    => 0,
            'oxactive' => 1,
            'oxtitle' => 'Nicht auf lager',
            'oxcontent' => '[{oxmultilang ident="MESSAGE_NOT_ON_STOCK"}]',
        ),
    ];

    public static function onActivate()
    {
        $sType = 'oxcategory';
        foreach (Events::$aCategories as $key => $value) {
            Events::createObject($sType, $key, $value);
        }

        $sType = 'oxcontent';
        foreach (Events::$aContents as $key => $value) {
            Events::createObject($sType, $key, $value);
        }
    }


    private static function createObject($type, $key, $value){
        $oObject = oxNew($type);
        if(!$oObject->load($key)){
            $oObject->assign($value);
            $oObject->save();
        }
    }

}