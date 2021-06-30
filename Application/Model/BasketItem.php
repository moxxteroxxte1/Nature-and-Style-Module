<?php


namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class BasketItem extends BasketItem_parent
{
    protected array $aDiscounts = [];

    public function addDiscount($dValue, $sType, $sDiscount)
    {
        $this->aDiscounts[$sDiscount] = array('id' => array('value' => $dValue, 'type' => $sType));
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
}