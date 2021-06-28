<?php


namespace NatureAndStyle\CoreModule\Application\Model;


class Delivery extends Delivery_parent
{

    protected $iAmount = 1;

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
        } else {
            $dAmount = parent::getDeliveryAmount($oBasketItem);
        }

        return $dAmount;
    }

    protected function _getMultiplier()
    {
        $dAmount = 0;

        if ($this->getCalculationRule() == self::CALCULATION_RULE_ONCE_PER_CART) {
            $dAmount = 1;
        } elseif ($this->getCalculationRule() == self::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT) {
            $dAmount = $this->_iProdCnt;
        } elseif ($this->getCalculationRule() == self::CALCULATION_RULE_FOR_EACH_PRODUCT) {
            $dAmount = $this->_iItemCnt;
        } elseif ($this->getCalculationRule() == self::CALCULATION_RULE_FIT_PER_CART) {
            $dAmount = $this->iAmount;
        }

        return $dAmount;
    }


    public function isForBasket($oBasket)
    {

        $blForBasket = false;
        $iAllPoints = 0;

        if ($this->checkArticleRestriction()) {
            if ($this->getCalculationRule() == self::CALCULATION_RULE_FIT_PER_CART) {
                foreach ($oBasket->getContents() as $oContent) {
                    $oArticle = $oContent->getArticle(false);
                    $dAmount = $oContent->getAmount();
                    $iDeliveryPoints = $oArticle->oxarticles__oxpackagingpoints->value;
                    $iAllPoints += ($dAmount * $iDeliveryPoints);
                }
                $this->iAmount = ceil(($iAllPoints / $this->getConditionTo()));
                $blForBasket = true;
            } else {
                $blForBasket = parent::isForBasket($oBasket);
            }
        }
        return $blForBasket;
    }

    protected function checkArticleRestriction($oArticle)
    {
        $sMinDel = $oArticle->getMinDelivery();

        if ($sMinDel) {
            return ($sMinDel == $this->oxdelivery__oxid->value) || $this->isParent($sMinDel, $this->oxdelivery__oxchildid->value);
        }

        return true;

    }

    protected function isParent($sID, $sChildId): bool
    {
        if ($sID == $sChildId) {
            return true;
        }

        if (!is_null($sChildId)) {
            $oDeliveryChild = oxNew(Delivery::class);
            $oDeliveryChild = $oDeliveryChild->load($sChildId);
            return $this->isParent($sID, $oDeliveryChild->oxdelivery__oxchildid->value);
        }

        return false;
    }

    public function getMultiplier()
    {
        return $this->_getMultiplier();
    }
}