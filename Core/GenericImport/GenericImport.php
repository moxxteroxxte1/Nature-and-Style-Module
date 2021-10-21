<?php


namespace NatureAndStyle\CoreModule\Core\GenericImport;

use NatureAndStyle\CoreModule\Core\GenericImport\ImportObject\Article;
use OxidEsales\Eshop\Core\Registry;

class GenericImport extends GenericImport_parent
{
    protected function createImportObject($type)
    {
        if($type==='A'){
            $logger = Registry::getLogger();
            $logger->warning('IMPORT ARTICLE');
            return oxNew(Article::class);
        }else{
            return parent::createImportObject($type);
        }
    }
}