<?php


namespace NatureAndStyle\CoreModule\Application\Model;


class DeliveryList extends DeliveryList_parent
{

    public function getDeliveryList($oBasket, $oUser = null, $sDelCountry = null, $sDelSet = null)
    {
        return parent::getDeliveryList($oBasket, $oUser, $sDelCountry, $sDelSet);

    }

}