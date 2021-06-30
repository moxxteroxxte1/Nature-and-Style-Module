<?php


namespace NatureAndStyle\CoreModule\Application\Model;


class BasketItem extends BasketItem_parent
{

    protected $aDiscounts = [];

    public function addDiscount($dValue, $sType){
        array_push($this->aDiscounts, array('value' => $dValue, 'type' => $sType));
    }

    public function setPrice($oPrice)
    {
        parent::setPrice();

        foreach ($this->aDiscounts as $discount){
            $this->getPrice()->setDiscount($discount['value'],$discount['type']);
        }
    }
}