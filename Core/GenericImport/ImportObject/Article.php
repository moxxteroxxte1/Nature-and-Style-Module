<?php


namespace NatureAndStyle\CoreModule\Core\GenericImport\ImportObject;

use OxidEsales\Eshop\Core\Registry;

class Article extends Article_parent
{

    public function getFieldList()
    {
        $fieldList = parent::getFieldList();
        array_push($fieldList , "OXLONGDESC");
        return $fieldList;
    }

}