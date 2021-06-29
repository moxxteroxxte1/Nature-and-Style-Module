<?php


namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class Delivery extends Delivery_parent
{

    var int $iAmount = 1;

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
            $dAmount = $oProduct->oxarticles__oxpackagingpoints->value;
        } else {
            $dAmount = parent::getDeliveryAmount($oBasketItem);
        }

        return $dAmount;
    }

    protected function _getMultiplier()
    {
        if ($this->getCalculationRule() == self::CALCULATION_RULE_FIT_PER_CART) {
            $logger = Registry::getLogger();
            $logger->debug($this->iAmount);
            return $this->iAmount;
        } else {
            return parent::_getMultiplier();
        }
    }


    public function isForBasket($oBasket)
    {

        $blForBasket = false;
        $iAllPoints = 0;

        if ($this->getCalculationRule() == self::CALCULATION_RULE_FIT_PER_CART) {

            $blForBasket = true;

            foreach ($oBasket->getContents() as $oContent) {
                $oArticle = $oContent->getArticle(false);
                $iDeliveryPoints = $this->getDeliveryAmount($oContent);

                if ($this->checkArticleRestriction($oArticle) && $this->isDeliveryRuleFitByArticle($iDeliveryPoints)) {

                    $dAmount = $oContent->getAmount();
                    $iAllPoints += ($dAmount * $iDeliveryPoints);
                } else {
                    return false;
                }
            }

            $this->iAmount = ceil(($iAllPoints / $this->getConditionTo()));
        } else {
            $blForBasket = parent::isForBasket($oBasket);
        }

        return $blForBasket;
    }

    protected function checkArticleRestriction($oArticle)
    {
        $sMinDel = $oArticle->getMinDelivery();
        $logger = Registry::getLogger();
        $logger->info(1);
        if (isset($sMinDel)) {
            $logger->info(2);
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