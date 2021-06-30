<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use stdClass;

class DeliveryMain extends DeliveryMain_parent
{

    protected array $aDelTypes = [];

    public function getDeliveryTypes()
    {
        $aDelTypes = array(
            array(
                "id" => "a",
                "name" => "amount"
            ),
            array(
                "id" => "s",
                "name" => "size"
            ),
            array(
                "id" => "w",
                "name" => "weight"
            ),
            array(
                "id" => "p",
                "name" => "price"
            ),
            array(
                "id" => "pp",
                "name" => "points"
            ),
        );

        foreach ($aDelTypes as $aDelType){
            $this->addDeliverype($aDelType['id'], $aDelType['name']);
        }

        return $aDelTypes;
    }

    protected function addDeliverype($id, $name)
    {
        $oLang = Registry::getLang();
        $iLang = $oLang->getTplLanguage();

        $oType = new stdClass();
        $oType->sType = $id;      // ParcelPoints
        $oType->sDesc = $oLang->translateString($name, $iLang);
        self::$aDelTypes[$id] = $oType;
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