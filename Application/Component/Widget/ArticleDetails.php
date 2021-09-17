<?php


namespace NatureAndStyle\CoreModule\Application\Component\Widget;


class ArticleDetails extends ArticleDetails_parent
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