<?php


namespace NatureAndStyle\CoreModule\Core\GenericImport;


use NatureAndStyle\CoreModule\Core\GenericImport\ImportObject\Article;

class GenericImport extends GenericImport_parent
{
    protected function createImportObject($type)
    {
        if($type=='A'){
            $className = get_class(Article::class);
        }else{
            $className = parent::createImportObject($type);
        }

        return oxNew($className);
    }
}