<?php

namespace NatureAndStyle\CoreModule\Core;

/**
 * Class defines what module does on Shop events.
 */
class Events {

    public static function onActivate()
    {
       self::createCategory();
       self::insertVECol();
    }

    public function insertVECol(){
    }

    public function createCategory(){
        $oCategory = oxNew('oxcategory');
        $oCategory->assign(array(
            'oxtitle' => 'Unikate',
            'oxactive' => 1,
            "oxparentid" => "oxrootid",
            "oxid" => 'unique',
            'oxsort' => 0
        ));
        $oCategory->save();
    }

}