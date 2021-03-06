<?php


namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\DatabaseProvider;

class Delivery extends Delivery_parent
{
    var int $iAmount = 1;
    protected bool $blIncludesSurcharge = false;
    protected bool $blIncludesCargo = false;
    protected int $iCargoMultiplier = 0;

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
        $blExclNonMaterial = Registry::getConfig()->getConfigParam('blExclNonMaterialFromDelivery');

        if ($oProduct->isOrderArticle()) {
            $oProduct = $oProduct->getArticle();
        }

        if ($oProduct->oxarticles__oxfreeshipping->value || ($oProduct->oxarticles__oxnonmaterial->value && $blExclNonMaterial)) {
            if ($this->_blFreeShipping !== false) {
                $this->_blFreeShipping = true;
            }
        } else {
            $this->_blFreeShipping = false;
            if ($this->getConditionType() == self::CONDITION_TYPE_POINTS) {
                $dAmount = $oProduct->oxarticles__oxpackagingpoints->value;
            } else {
                $dAmount = parent::getDeliveryAmount($oBasketItem);
            }
        }
        return $dAmount;
    }

    public function _getMultiplier()
    {
        if ($this->getCalculationRule() == self::CALCULATION_RULE_FIT_PER_CART) {
            return $this->iAmount;
        } else {
            return parent::_getMultiplier();
        }
    }

    protected function getCostSum()
    {
        $dPrice = $this->getBasePrice();
        $dPrice += $this->getCargoPrice();
        return $dPrice;
    }

    protected function getBasePrice()
    {
        if ($this->getAddSumType() == 'abs') {
            $oCur = Registry::getConfig()->getActShopCurrencyObject();
            $dPrice = $this->getAddSum() * $oCur->rate * $this->getMultiplier();
        } else {
            $dPrice = $this->_dPrice / 100 * $this->getAddSum();
        }
        return $dPrice;
    }


    public function isForBasket($oBasket)
    {
        $blForBasket = false;
        $iAllPoints = 0;

        if ($this->getCalculationRule() == self::CALCULATION_RULE_FIT_PER_CART) {
            $blForBasket = true;
            foreach ($oBasket->getContents() as $oContent) {
                $oArticle = $oContent->getArticle(false);

                if ($this->checkArticleRestriction($oArticle)) {
                    $dAmount = $oContent->getAmount();
                    if ($oArticle->oxarticles__oxbulkygood->value && !$this->includeCargo() && $this->getBasePrice() > 0) {
                        $this->_blFreeShipping = false;
                        $this->iCargoMultiplier += ($oArticle->oxarticles__oxbulkygoodmultiplier->value * ($oArticle->oxarticles__oxbulkygoodmultiplier->value > 1 ? $dAmount : 1));
                        $dAmount *= max($oArticle->oxarticles__oxbulkygoodmultiplier->value, 1);
                        $this->blIncludesCargo = true;
                    }
                    $iAllPoints += ($dAmount * $this->getDeliveryAmount($oContent));
                } else {
                    return false;
                }
            }
            $this->iAmount = $iAllPoints > 0 ? ceil(($iAllPoints / $this->getConditionTo())) : 0;
        } else {
            $blForBasket = parent::isForBasket($oBasket);
        }
        return $blForBasket;
    }

    protected function checkArticleRestriction($oArticle)
    {
        $sMinDel = $oArticle->getMinDelivery();
        $blFit = true;

        if (isset($sMinDel) && $sMinDel != '----' && $sMinDel != 0) {
            $blFit = (($sMinDel == $this->oxdelivery__oxid->value) || $this->isParent($sMinDel, $this->oxdelivery__oxchildid->value));
        }

        $iDeliveryAmount = $oArticle->oxarticles__oxpackagingpoints->value;
        $blFit &= (($iDeliveryAmount >= $this->getConditionFrom() && $iDeliveryAmount <= $this->getConditionTo()) || (!$this->includeCargo() && $oArticle->oxarticles__oxbulkygood->value));

        return $blFit;
    }

    protected function isParent($sID, $sChildId): bool
    {
        if ($sID == $sChildId) {
            return true;
        }

        if (!is_null($sChildId)) {
            $oDb = DatabaseProvider::getDb();
            $sQ = "select oxchildid from oxdelivery where oxid = {$oDb->quote($sChildId)}";
            $resultSet = $oDb->select($sQ);

            if ($resultSet && $resultSet->count() > 0) {
                while (!$resultSet->EOF) {
                    $row = $resultSet->getFields();

                    $sChildId = (string)$row[0];

                    $resultSet->fetchRow();
                }
                return $this->isParent($sID, $sChildId);
            }
        }

        return false;
    }

    public function getMultiplier()
    {
        return $this->_getMultiplier();
    }

    /**
     * @return bool
     */
    public function isBlIncludesSurcharge(): bool
    {
        return $this->blIncludesSurcharge;
    }

    /**
     * @param bool $blIncludesSurcharge
     */
    public function setBlIncludesSurcharge(bool $blIncludesSurcharge = true): void
    {
        $this->blIncludesSurcharge = $blIncludesSurcharge;
    }

    public function isIncludingCargo()
    {
        return $this->blIncludesCargo;
    }

    public function getCargoPrice()
    {
        $dCargoPrice = 0;
        if (!$this->includeCargo() && $this->blIncludesCargo) {
            $dCargoPrice = doubleval(Registry::getConfig()->getConfigParam('nascargoprice')) * $this->iCargoMultiplier;
        }
        return $dCargoPrice;
    }

    public function includeCargo()
    {
        return $this->isParent(Registry::getConfig()->getConfigParam('nascargodelivery'), $this->getId());
    }

    public function isMarkedShipping()
    {
        return $this->oxdelivery__oxmarkshipping->value;
    }

    public function getDeliveryPrice($dVat = null)
    {
        if ($this->_oPrice === null) {

            $session = Registry::getSession();
            $oUser = $session->getBasket()->getBasketUser();

            $oPrice = oxNew(Price::class);
            $oPrice->setNettoMode((Registry::getConfig()->getConfigParam('blDeliveryVatOnTop') || $oUser->inGroup('oxiddealer')));
            $oPrice->setVat($dVat);

            // if article is free shipping, price for delivery will be not calculated
            if (!$this->_blFreeShipping) {
                $oPrice->add($this->getCostSum());
            }
            $this->setDeliveryPrice($oPrice);
        }

        return $this->_oPrice;
    }
}