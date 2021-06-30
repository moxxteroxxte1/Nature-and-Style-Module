<?php


namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class BasketItem extends BasketItem_parent
{

    protected $dBasePrice = 0;
    protected $aDiscounts = [];

    public function addDiscount($dValue, $sType){
        array_push($this->aDiscounts, array('value' => $dValue, 'type' => $sType));
        $logger = Registry::getLogger();
        foreach ($this->aDiscounts as $discount){
            $logger->info(implode(" ", $discount));
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