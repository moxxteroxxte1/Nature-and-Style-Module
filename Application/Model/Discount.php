<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class Discount extends Discount_parent
{
    public function isForBasketAmount($oBasket)
    {
        if ($this->oxdiscount__oxamountpackageunit->value) {
            $dAmount = 0;
            $aBasketContents = $oBasket->getContents();
            foreach ($aBasketContents as $oBasketItem) {
                $oBasketArticle = $oBasketItem->getArticle(true);

                $blForBasketItem = ($this->oxdiscount__oxaddsumtype->value != 'itm' ?
                    $this->isForBasketItem($oBasketArticle) :
                    $this->isForBundleItem($oBasketArticle));

                if ($blForBasketItem) {
                    $dAmount += $oBasketItem->getAmount();
                    $dPackUnit = $oBasketArticle->getPackagingUnit();
                    if ($dPackUnit > 1 && ($dAmount % $dPackUnit == 0)) {
                        $oBasketItem->addDiscount($this->getAddSum(), $this->getAddSumType());
                    }
                }
            }
            return false;
        }
        return parent::isForBasketAmount($oBasket);
    }

    public
    function getShortDesc()
    {
        return $this->oxdiscount__oxshortdesc->value;
    }

}

