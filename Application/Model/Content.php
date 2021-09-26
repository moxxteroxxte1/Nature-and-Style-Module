<?php


namespace NatureAndStyle\CoreModule\Application\Model;


class Content extends Content_parent
{

    public function getSubCats()
    {
        $oContentList = oxNew(ContentList::class);
        $oContentList->loadSubCats($this->getId());
        return $oContentList->getAsArray();
    }

}