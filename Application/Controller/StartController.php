<?php


namespace NatureAndStyle\CoreModule\Application\Controller;


use OxidEsales\Eshop\Application\Model\Actions;
use OxidEsales\Eshop\Application\Model\ActionList;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\DatabaseProvider;

class StartController extends StartController_parent
{

    public function getTiles()
    {
        $oTilesList = null;

        if (Registry::getConfig()->getConfigParam('bl_perfLoadAktion')) {
            $oTilesList = oxNew(ActionList::class);
            $oTilesList->loadTiles();
        }

        return $oTilesList;
    }

    public function getShopUrl()
    {
        return Registry::getConfig()->getShopUrl();
    }

    public function getBargainShortDescription()
    {
        $oDb = DatabaseProvider::getDb();
        $sQ = "SELECT oxlongdesc FROM oxactions WHERE oxid = 'oxbargain'";
        $resultSet = $oDb->select($sQ);

        if ($resultSet && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $row = $resultSet->getFields();
                return (string)$row[0];

            }
        }
        return null;
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