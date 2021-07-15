<?php


namespace NatureAndStyle\CoreModule\Application\Model;

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

    protected function _getCostSum()
    {
        return (parent::_getCostSum() + $this->getCargoPrice());
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
                    if ($oArticle->oxarticles__oxbulkygood->value && !$this->includeCargo()) {
                        $this->iCargoMultiplier += $oArticle->oxarticles__oxbulkygoodmultiplier->value;
                        $this->blIncludesCargo = true;
                    } else {
                        $iAllPoints += ($dAmount * $this->getDeliveryAmount($oContent));
                    }
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

        if (isset($sMinDel) && $sMinDel != '----') {
            $blFit = (($sMinDel == $this->oxdelivery__oxid->value) || $this->isParent($sMinDel, $this->oxdelivery__oxchildid->value));
        }

        $iDeliveryAmount = $oArticle->oxarticles__oxpackagingpoints->value;
        $blFit &= ($iDeliveryAmount >= $this->getConditionFrom() && $iDeliveryAmount <= $this->getConditionTo());

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
        return (!$this->includeCargo() && $this->blIncludesCargo) ? (doubleval(Registry::getConfig()->getConfigParam('nascargoprice')) * $this->iCargoMultiplier) : 0;
    }

    public function includeCargo()
    {
        return $this->isParent(Registry::getConfig()->getConfigParam('nascargodelivery'), $this->getId());
    }
}