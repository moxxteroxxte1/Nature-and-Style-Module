<?php


namespace NatureAndStyle\CoreModule\Application\Model;


class Delivery extends Delivery_parent
{

    protected $iAmount = 0;

    /**
     * Calculation rule
     */
    const CALCULATION_RULE_FIT_PER_CART = 3;

    /**
     * Condition type
     */
    const CONDITION_TYPE_POINTS = 'pp';

    public function getDeliveryAmount($oBasketItem)
    {
        $dAmount = 0;
        $oProduct = $oBasketItem->getArticle(false);

        if ($oProduct->isOrderArticle()) {
            $oProduct = $oProduct->getArticle();
        }

        if ($this->getConditionType() == self::CONDITION_TYPE_POINTS) {
            $dAmount += $oProduct->oxarticles__oxpackagingpoints->value;
        }else{
            $dAmount = parent::getDeliveryAmount($oBasketItem);
        }

        return $dAmount;
    }

}