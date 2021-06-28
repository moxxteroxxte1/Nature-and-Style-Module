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
        $data = [];

        $logger = Registry::getLogger();

        $oDb = DatabaseProvider::getDb();
        $sQ = "select oxid, oxtitle from oxdelivery where oxid != {$oDb->quote($this->getEditObjectId())}";

        $resultSet  = $oDb->select($sQ);

        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $row = $resultSet->getFields();

                $id = (string)$row[0];
                $title = (string)$row[1];

                $data[$id] = $title;
                $resultSet->fetchRow();
            }
        }

        return $resultSet;
    }
}