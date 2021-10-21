<?php


namespace NatureAndStyle\CoreModule\Core\GenericImport\ImportObject;


class Article extends Article_parent
{

    public function getFieldList()
    {
        $fieldList = parent::getFieldList();
        array_push($fieldList, "OXLONGDESC");
        return $fieldList ;
    }

}