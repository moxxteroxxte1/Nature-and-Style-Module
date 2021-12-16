<?php

namespace NatureAndStyle\CoreModule\Application\Model;

class DiscountList extends DiscountList_parent
{

    public function getPotenzialDiscounts($oArticle, $oUser = null)
    {
        $aList = [];
        $aDiscList = $this->_getList($oUser)->getArray();
        foreach ($aDiscList as $oDiscount) {
            if ($oDiscount->checkArticle($oArticle)) {
                $aList[$oDiscount->getId()] = $oDiscount;
            }
        }

        return $aList;
    }

}