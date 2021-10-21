<?php


namespace NatureAndStyle\CoreModule\Core\GenericImport;

use NatureAndStyle\CoreModule\Core\GenericImport\ImportObject\Article;
use OxidEsales\Eshop\Core\Registry;

class GenericImport extends GenericImport_parent
{
    protected function createImportObject($type)
    {
        $logger = Registry::getLogger();
        $logger->warning($type);
        if($type=='Article'){
            return oxNew(Article::class);
        }else{
            return parent::createImportObject($type);
        }
    }
}