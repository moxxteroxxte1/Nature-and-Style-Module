<?php


namespace NatureAndStyle\CoreModule\Core\GenericImport\ImportObject;

use mysql_xdevapi\Warning;
use OxidEsales\Eshop\Core\Registry;

class Article extends Article_parent
{

    public function getFieldList()
    {
        $fieldList = parent::getFieldList();
        $logger = Registry::getLogger();
        foreach($fieldList as $key => $value) {
            $logger->warning("$key is at $value");
        }
        array_push($fieldList , "OXLONGDESC");
        return $fieldList;
    }

}