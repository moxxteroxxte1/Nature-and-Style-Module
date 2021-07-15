<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class ShopConfiguration extends ShopConfiguration_parent
{

    public function render()
    {
        $deliverys = $this->getAllDeliverys();
        $this->_aViewData["deliverys"] = $deliverys;

        return parent::render();
    }

    public function getAllDeliverys()
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
                $blSelected = strcmp(Registry::getConfig()->getConfigParam('nascargodelivery'), $sId) == 0;

                $currentDelivery = array($sId, $sTitle, $blSelected);
                $logger = Registry::getLogger();
                $logger->warning(implode(" ,", $currentDelivery));
                array_push($data, $currentDelivery);

                $resultSet->fetchRow();
            }
        }
        return $resultSet;
    }
}