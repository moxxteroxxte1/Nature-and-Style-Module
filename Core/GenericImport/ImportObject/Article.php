<?php


namespace NatureAndStyle\CoreModule\Core\GenericImport\ImportObject;

use OxidEsales\Eshop\Core\Registry;

class Article extends Article_parent
{

    public function getFieldList()
    {
        parent::getFieldList();
        $logger = Registry::getLogger();
        $logger->info(each($this->fieldList));
        array_push($this->fieldList , "OXLONGDESC");
        return $this->fieldList;
    }

}