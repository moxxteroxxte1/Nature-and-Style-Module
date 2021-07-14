<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


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

                $id = (string)$row[0];
                $title = (string)$row[1];

                array_push($data, array($id, $title));
                $resultSet->fetchRow();
            }
        }
        return $resultSet;
    }
}