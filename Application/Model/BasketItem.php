<?php


namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class BasketItem extends BasketItem_parent
{

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
        parent::setPrice($oPrice);

        $logger = Registry::getLogger();
        $logger->info("2 " . $this->getPrice()->getPrice());

        foreach ($this->aDiscounts as $discount){
            $this->getPrice()->setDiscount($discount['value'],$discount['type']);
        }
        $this->getPrice()->calculateDiscount();
        $logger->info("3 " . $this->getPrice()->getPrice());
    }
}