<?php


namespace NatureAndStyle\CoreModule\Application\Controller;


use OxidEsales\Eshop\Application\Model\ActionList;
use OxidEsales\Eshop\Core\Registry;

class StartController extends StartController_parent
{

    public function getTiles()
    {
        $oTilesList = null;

        if(Registry::getConfig()->getConfigParam('bl_perfLoadAktion')){
            $oTilesList = oxNew(ActionList::class);
            $oTilesList->loadTiles();
        }

        return $oTilesList;
    }

    public function getShopUrl(){
        return Registry::getConfig()->getShopUrl();
    }

    public function getBargainShortDescription()
    {
        $oAction = oxNew(Action::class);
        $oAction->load('oxbargain');
        return $oAction->oxactions__oxlongdesc->value;
    }

}