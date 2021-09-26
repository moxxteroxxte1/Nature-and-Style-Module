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

        $aArray = [];
        if(count($oContentList) > 0){
            foreach ($oContentList as $oContent){
                array_push($aArray, $oContent);
            }
        }
        $this->_aViewData['contcats'] = $aArray;
        $logger = Registry::getLogger();
        $logger->info(explode($this->_aViewData['contcats']));
        return "content_main.tpl";

    }


}