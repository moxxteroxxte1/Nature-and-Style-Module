<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use stdClass;

class DeliveryMain extends DeliveryMain_parent
{

    public function getDeliveryTypes()
    {
        $oLang = Registry::getLang();
        $iLang = $oLang->getTplLanguage();

        $aDelTypes = [];
        $oType = new stdClass();
        $oType->sType = "a";      // amount
        $oType->sDesc = $oLang->translateString("amount", $iLang);
        $aDelTypes['a'] = $oType;
        $oType = new stdClass();
        $oType->sType = "s";      // Size
        $oType->sDesc = $oLang->translateString("size", $iLang);
        $aDelTypes['s'] = $oType;
        $oType = new stdClass();
        $oType->sType = "w";      // Weight
        $oType->sDesc = $oLang->translateString("weight", $iLang);
        $aDelTypes['w'] = $oType;
        $oType = new stdClass();
        $oType->sType = "p";      // Price
        $oType->sDesc = $oLang->translateString("price", $iLang);
        $aDelTypes['p'] = $oType;
        $oType = new stdClass();
        $oType->sType = "pp";      // ParcelPoints
        $oType->sDesc = $oLang->translateString("points", $iLang);
        $aDelTypes['pp'] = $oType;

        return $aDelTypes;
    }

    public function getAllDeliverys()
    {
        $aResult = [];
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $sQ = "select oxid, oxtitle from oxdelivery where oxid != {$oDb->quote($this->getEditObjectId())}";

        $aResultSet = $oDb->select($sQ, []);

        $logger = Registry::getLogger();
        foreach ($aResultSet as $item){
            $logger->warning(implode($item));
            $logger->error(implode($item[0]));
        }



        return $aResult;
    }
}