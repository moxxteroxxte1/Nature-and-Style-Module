<?php


namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class BasketItem extends BasketItem_parent
{
    protected array $aDiscounts = [];
    protected array $aAppliedDiscounts = [];

    public function addDiscount($dValue, $sType, $sDiscount){
        if(!in_array($sDiscount,$this->aAppliedDiscounts)){
            array_push($this->aDiscounts, array('value' => $dValue, 'type' => $sType));
            array_push($this->aAppliedDiscounts, $sDiscount);
        }
    }

    public function setPrice($oPrice)
    {
        foreach ($this->aDiscounts as $key => $discount){
            $oPrice->setDiscount($discount['value'],$discount['type']);
            unset($this->aDiscounts[$key]);
        }
        $oPrice->calculateDiscount();

        $this->_oUnitPrice = clone $oPrice;
        $this->_oPrice = clone $oPrice;

        $this->_oPrice->multiply($this->getAmount());
    }
}