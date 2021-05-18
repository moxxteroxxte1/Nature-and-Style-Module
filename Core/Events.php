<?php

namespace NatureAndStyle\CoreModule\Core;

/**
 * Class defines what module does on Shop events.
 */
class Events
{

    public static function onActivate()
    {
        $oCategory = oxNew('oxcategory');
        $oCategory->load('new_articles');
        if ($oCategory === null) {
            $oCategory->assign(array(
                "oxid" => 'new_articles',
                'oxtitle' => 'Unsere Neuheiten',
                'oxactive' => 1,
                "oxparentid" => "oxrootid",
                'oxsort' => 0
            ));
            $oCategory->save();
        }
    }
}