<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


use NatureAndStyle\CoreModule\Application\Model\ContentList;

class ContentMain extends ContentMain_parent
{

    public function render(){
        $oContentList = oxNew(ContentList::class);
        $oContentList->loadCatMenues();

        $this->_aViewData['contcats'] = $oContentList;
        return parent::render();

    }

}