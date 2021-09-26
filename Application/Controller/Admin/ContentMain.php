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
        $aArray = [];
        if(count($oContentList) > 0){
            foreach ($oContentList as $oContent){
                $logger = Registry::getLogger($oContent);
                //$aArray = $oContent;
            }
        }
        $this->_aViewData['contcats'] = $aArray;
        return "content_main.tpl";

    }


}