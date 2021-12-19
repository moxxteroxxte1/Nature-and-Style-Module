<?php

namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class ShopConfiguration extends ShopConfiguration_parent
{

    public function render()
    {
        $deliveries = $this->getAllDeliveries();
        $shipSets = $this->getAllShipSets();
        $this->_aViewData["deliveries"] = $deliveries;
        $this->_aViewData["shipsets"] = $shipSets;

        return parent::render();
    }

    public function getAllDeliveries()
    {
        $data = [];

        $oDb = DatabaseProvider::getDb();
        $sQ = "select oxid, oxtitle from oxdelivery where oxid != {$oDb->quote($this->getEditObjectId())}";

        $resultSet = $oDb->select($sQ);

        if ($resultSet && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $row = $resultSet->getFields();

                $sId = (string)$row[0];
                $sTitle = (string)$row[1];
                $sSelected = Registry::getConfig()->getConfigParam('nascargodelivery') == $sId ? "selected" : "";

                array_push($data, array($sId, $sSelected, $sTitle));
                $resultSet->fetchRow();
            }
        }
        return $data;
    }

    public function getAllShipSets()
    {
        $data = [];

        $oDb = DatabaseProvider::getDb();
        $sQ = "select oxid, oxtitle from oxdeliveryset where oxid != {$oDb->quote($this->getEditObjectId())}";

        $resultSet = $oDb->select($sQ);

        if ($resultSet && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $row = $resultSet->getFields();

                $sId = (string)$row[0];
                $sTitle = (string)$row[1];
                $sSelected = Registry::getConfig()->getConfigParam('nasdefaultshipset') == $sId ? "selected" : "";

                array_push($data, array($sId, $sSelected, $sTitle));
                $resultSet->fetchRow();
            }
        }
        return $data;
    }
}