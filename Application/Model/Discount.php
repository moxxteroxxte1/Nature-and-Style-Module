<?php

namespace NatureAndStyle\CoreModule\Application\Model;

class Discount extends Discount_parent
{
    public function isForBasketAmount($oBasket)
    {
        $dAmount = 0;
        $aBasketItems = $oBasket->getContents();
        foreach ($aBasketItems as $oBasketItem) {
            $oBasketArticle = $oBasketItem->getArticle(false);

            $this->oxdiscount__oxaddsumtype->value != 'itm' ?
                $blForBasketItem = $this->isForBasketItem($oBasketArticle) :
                $blForBasketItem = $this->isForBundleItem($oBasketArticle);


            if ($blForBasketItem) {
                $dRate = $oBasket->getBasketCurrency()->rate;
                if ($this->oxdiscount__oxprice->value) {
                    if (($oPrice = $oBasketArticle->getPrice())) {
                        $dAmount += ($oPrice->getPrice() * $oBasketItem->getAmount()) / $dRate;
                    }
                } elseif ($this->oxdiscount__oxamount->value && $this->oxdiscount__oxamountpackageunit->value) {
                    $dAmount += $oBasketItem->getAmount();
                    $dPackUnit = $oBasketArticle->getPackagingUnit();
                    return ($dPackUnit > 1 && ($dAmount % $dPackUnit == 0));
                }
            }
        }

        return $this->isForAmount($dAmount);
    }

    public function getShortDesc()
    {
        return $this->oxdiscount__oxshortdesc->value;
    }

}

