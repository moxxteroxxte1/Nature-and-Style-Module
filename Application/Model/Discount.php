<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class Discount extends Discount_parent
{
    public function isForBasketAmount($oBasket)
    {
        if ($this->oxdiscount__oxamountpackageunit->value) {
            $aBasketContents = $oBasket->getContents();
            foreach ($aBasketContents as $oBasketItem) {
                $oBasketArticle = $oBasketItem->getArticle(false);

                $blForBasketItem = ($this->oxdiscount__oxaddsumtype->value != 'itm' ?
                    $this->isForBasketItem($oBasketArticle) :
                    $this->isForBundleItem($oBasketArticle));

                if ($blForBasketItem) {
                    $dAmount = $oBasketItem->getAmount();
                    $dPackUnit = $oBasketArticle->getPackagingUnit();
                    if ($dPackUnit > 1 && ($dAmount % $dPackUnit == 0)) {
                        $oBasketItem->addDiscount($this->getAddSum(), $this->getAddSumType(), $this->getId());
                    }
                }
            }
            return false;
        }
        return parent::isForBasketAmount($oBasket);
    }

    public function getArticleDiscounts($oArticle, $oUser = null)
    {
        $aList = [];
        $aDiscList = $this->_getList($oUser)->getArray();
        foreach ($aDiscList as $oDiscount) {
            if ($oDiscount->isForArticle($oArticle)) {
                $aList[$oDiscount->getId()] = $oDiscount;
            }
        }

        return $aList;
    }

    public function isForArticle($oArticle)
    {
        $logger = Registry::getLogger();
        // item discounts may only be applied for basket
        if ($this->oxdiscount__oxaddsumtype->value == 'itm') {
            $logger->error('1');
            return false;
        }

        if (($this->oxdiscount__oxamount->value || $this->oxdiscount__oxprice->value) && !$this->oxdiscount__oxamountpackageunit->value) {
            $logger->error('2');
            return false;
        }

        if ($this->oxdiscount__oxpriceto->value && ($this->oxdiscount__oxpriceto->value < $oArticle->getBasePrice())) {
            $logger->error('3');
            return false;
        }

        if ($this->isGlobalDiscount()) {
            return true;
        }

        $sArticleId = $oArticle->getProductId();

        if (!isset($this->_aHasArticleDiscounts[$sArticleId])) {
            $blResult = $this->isArticleAssigned($oArticle) || $this->isCategoriesAssigned($oArticle->getCategoryIds());

            $this->_aHasArticleDiscounts[$sArticleId] = $blResult;
        }

        return $this->_aHasArticleDiscounts[$sArticleId];
    }

    public function getShortDesc()
    {
        return $this->oxdiscount__oxshortdesc->value;
    }

    public function fitPackagingUnit()
    {
        return $this->oxdiscount__oxamountpackageunit->value;
    }
}

