<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use NatureAndStyle\CoreModule\Application\Model\ContentList;
use OxidEsales\Eshop\Core\Registry;

class ContentMain extends ContentMain_parent
{

    public function render(){
        parent::render();
        $oContentList = oxNew(ContentList::class);
        $oContentList->loadCatMenues();
        $logger = Registry::getLogger();
        $logger->info(count($oContentList));
        $this->_aViewData['contcats'] = $oContentList;
        return "content_main.tpl";

    }


}