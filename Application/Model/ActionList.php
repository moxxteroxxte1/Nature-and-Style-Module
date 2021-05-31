<?php


namespace NatureAndStyle\CoreModule\Application\Model;


class ActionList extends ActionsList_parent
{

    public function loadTiles(){
        $oBaseObject = $this->getBaseObject();
        $oViewName = $oBaseObject->getViewName();
        $sQ = "select * from {$oViewName} where oxtype=4 and " . $oBaseObject->getSqlActiveSnippet()
            . " and oxshopid='" . \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId() . "' " . $this->_getUserGroupFilter()
            . " order by oxsort";
        $this->selectString($sQ);
    }

}