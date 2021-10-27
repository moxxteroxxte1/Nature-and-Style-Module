<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use NatureAndStyle\CoreModule\Application\Model\Content;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class ContentMain extends ContentMain_parent
{

    public function render()
    {
        parent::render();

        $logger = Registry::getLogger();

        $aArray = [];
        $oDb = DatabaseProvider::getDb();

        $sSQL = "SELECT oxid, oxtitle FROM oxcontents WHERE  `oxactive` = '1' AND oxtype = '2' AND `oxcatid` IS NOT NULL AND `oxsnippet` = '0' ORDER BY `oxloadid`";
        $rs = $oDb->select($sSQL);
        $rs = $rs->fetchAll();

        foreach ($rs as $result) {
            array_push($this->_aViewData['contcats'], ['id' => $result[0], 'title' => $result[1]]);
        }

        return "content_main.tpl";

    }

    public function getSubCats()
    {
        $logger = Registry::getLogger();
        $aArray = [];
        $oDb = DatabaseProvider::getDb();

        $sSQL = "SELECT oxid, oxtitle FROM oxcontents WHERE `oxactive` = '1' AND oxtype = '2' AND `oxcatid` IS NOT NULL AND `oxsnippet` = '0' ORDER BY `oxloadid`";
        $rs = $oDb->select($sSQL);
        $rs = $rs->fetchAll();

        $aSubCats = array();

        foreach ($rs as $result) {
            $aSubCats[$result[0]] = $result[1];
        }
        return $aSubCats;
    }


}