<?php


namespace NatureAndStyle\CoreModule\Core\GenericImport;


use NatureAndStyle\CoreModule\Core\GenericImport\ImportObject\Article;

class GenericImport extends GenericImport_parent
{
    protected function createImportObject($type)
    {
        if($type=='A'){
            return oxNew(Article::class);
        }else{
            return parent::createImportObject($type);
        }
    }
}