<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

class ArticleStock extends ArticleStock_parent
{
    public function getAllDeliveries()
    {
        $data = [];

        $oDb = DatabaseProvider::getDb();
        $sQ = "select oxid, oxtitle from oxdelivery";

        $resultSet = $oDb->select($sQ);

        if ($resultSet != false && $resultSet->count() > 0) {
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