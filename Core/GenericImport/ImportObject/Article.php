<?php


namespace NatureAndStyle\CoreModule\Core\GenericImport\ImportObject;

use mysql_xdevapi\Warning;
use OxidEsales\Eshop\Core\Registry;

class Article extends Article_parent
{

    public function getFieldList()
    {
        parent::getFieldList();
        $logger = Registry::getLogger();
        foreach($this->fieldList as $key => $value) {
            $logger->warning("$key is at $value");
        }
        array_push($this->fieldList , "OXLONGDESC");
        return $this->fieldList;
    }

}