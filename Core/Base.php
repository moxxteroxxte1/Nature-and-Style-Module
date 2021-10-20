<?php


namespace NatureAndStyle\CoreModule\Core;

use OxidEsales\Eshop\Core\DatabaseProvider;

class Base extends Base_parent
{
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
}