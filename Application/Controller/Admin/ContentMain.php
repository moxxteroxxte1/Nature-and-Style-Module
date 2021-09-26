<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


use NatureAndStyle\CoreModule\Application\Model\ContentList;
use OxidEsales\Eshop\Core\Registry;

class ContentMain extends ContentMain_parent
{

    public function render(){
        $oContentList = oxNew(ContentList::class);
        $oContentList->loadCatMenues();

        $this->_aViewData['contcats'] = $oContentList;
        $logger = Registry::getLogger();
        $logger->info(explode(" ,", $this->_aViewData['contcats']));
        return parent::render();

    }

}