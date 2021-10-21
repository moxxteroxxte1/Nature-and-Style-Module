<?php


namespace NatureAndStyle\CoreModule\Core\GenericImport\ImportObject;


class Article extends Article_parent
{

    public function getFieldList()
    {
        parent::getFieldList();
        array_push($this->fieldList , "OXLONGDESC");
        return $this->fieldList;
    }

}