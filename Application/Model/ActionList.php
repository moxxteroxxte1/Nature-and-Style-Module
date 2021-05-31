<?php

namespace NatureAndStyle\CoreModule\Application\Model;

class ActionList extends ActionList_parent
{

    public function loadTiles(){
        $oBaseObject = $this->getBaseObject();
        $oViewName = $oBaseObject->getViewName();
        $sQ = "select * from {$oViewName} where oxtype=4 and " . $oBaseObject->getSqlActiveSnippet()
            . " and oxshopid='" . \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId() . "' " . $this->_getUserGroupFilter()
            . " order by oxsort";

        $this->clear();
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        if ($this->_aSqlLimit[0] || $this->_aSqlLimit[1]) {
            $rs = $oDb->selectLimit($sQ, $this->_aSqlLimit[1], max(0, $this->_aSqlLimit[0]), []);
        } else {
            $rs = $oDb->select($sQ, []);
        }

        if ($rs != false && $rs->count() > 0) {
            $oSaved = clone oxNew(\NatureAndStyle\CoreModule\Application\Model\Tile::class);

            while (!$rs->EOF) {
                $oListObject = clone $oSaved;

                $this->_assignElement($oListObject, $rs->fields);

                $this->add($oListObject);

                $rs->fetchRow();
            }
        }
    }


}