<?php


namespace NatureAndStyle\CoreModule\Application\Model;


class DeliveryList
{

    public function getDeliveryList($oBasket, $oUser = null, $sDelCountry = null, $sDelSet = null)
    {
        $aDeliveryList = parent::getDeliveryList($oBasket, $oUser, $sDelCountry, $sDelSet);
        return usort($aDeliveryList, function ($a,$b){return $a-getDeliveryPrice()->getPrice() <=> $b-getDeliveryPrice()->getPrice();});
    }

}