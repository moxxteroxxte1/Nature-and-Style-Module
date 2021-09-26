<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use NatureAndStyle\CoreModule\Application\Model\Content;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class ContentMain extends ContentMain_parent
{

    protected $_aViewData = [];

    public function render()
    {
        parent::render();

        $logger = Registry::getLogger();

        $aArray = [];
        $oDb = DatabaseProvider::getDb();

        $sSQL = "SELECT oxloadid FROM oxcontents WHERE  `oxactive` = '1' AND oxtype = '2' AND `oxcatid` IS NOT NULL AND `oxsnippet` = '0' ORDER BY `oxloadid`";
        $rs = $oDb->select($sSQL);
        $rs = $rs->fetchAll();

        foreach ($rs as $result) {
            $logger->info($result[0]);
            $oContent = oxnew(Content::class);
            $oContent->loadByIdent($result[0], true);

            $aArray[$oContent->oxcontents__oxid->value] = $oContent;
        }

        $this->_aViewData['contcats'] = $aArray;
        $logger->info(explode($this->_aViewData['contcats']));
        return "content_main.tpl";

    }


}