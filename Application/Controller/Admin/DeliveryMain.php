<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use stdClass;

class DeliveryMain extends DeliveryMain_parent
{

    public function getDeliveryTypes()
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
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
}