<?php


namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class BasketItem extends BasketItem_parent
{

    protected $aDiscounts = [];

    public function addDiscount($dValue, $sType, $sDicountId){
        array_push($this->aDiscounts, array('value' => $dValue, 'type' => $sType, 'discount' => $sDicountId));
        $logger = Registry::getLogger();
        foreach ($this->aDiscounts as $discount){
            $logger->info(implode(" ", $discount));
        }
    }

    public function setPrice($oPrice)
    {
        foreach ($this->aDiscounts as $discount){
            $oPrice->setDiscount($discount['value'],$discount['type']);
        }

        $this->_oUnitPrice = clone $oPrice;

        $this->_oPrice = clone $oPrice;
        $this->_oPrice->multiply($this->getAmount());
    }
}