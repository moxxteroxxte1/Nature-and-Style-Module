<?php


namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class BasketItem extends BasketItem_parent
{
    protected array $aDiscounts = [];

    public function addDiscount($dValue, $sType)
    {
        array_push($this->aDiscounts, array('value' => $dValue, 'type' => $sType));
    }

    public function setPrice($oPrice)
    {
        foreach ($this->aDiscounts as $key => $discount) {
            $oPrice->setDiscount($discount['value'], $discount['type']);
            unset($this->aDiscounts[$key]);
        }
        $oPrice->calculateDiscount();

        $this->_oUnitPrice = clone $oPrice;
        $this->_oPrice = clone $oPrice;

        $this->_oPrice->multiply($this->getAmount());
    }

    public function getAppliedDiscounts()
    {
        return $this->aAppliedDiscounts;
    }
}