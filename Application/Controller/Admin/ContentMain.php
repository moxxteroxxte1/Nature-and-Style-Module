<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use NatureAndStyle\CoreModule\Application\Model\Content;
use OxidEsales\Eshop\Core\DatabaseProvider;

class ContentMain extends ContentMain_parent
{

    public function render()
    {
        parent::render();

        $aArray = [];
        $oDb = DatabaseProvider::getDb();

        $sSQL = "SELECT oxloadid FROM oxcontents WHERE  `oxactive` = '1' AND oxtype = 2 AND `oxcatid` IS NOT NULL AND `oxsnippet` = '0' AND `oxshopid` = " . $oDb->quote($this->_sShopID) . " ORDER BY `oxloadid`";
        $rs = $oDb->select($sSQL);
        $rs = $rs->fetchAll();

        foreach ($rs as $row) {
            $oContent = oxnew(Content::class);
            $oContent->loadByIdent($row[0], true);
            array_push($aArray, $oContent);
        }

        $this->_aViewData['contcats'] = $aArray;
        return "content_main.tpl";

    }


}