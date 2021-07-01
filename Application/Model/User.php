<?php

namespace NatureAndStyle\CoreModule\Application\Model;

class User extends User_parent{

    public function isPriceViewModeNetto()
    {
        return $this->inGroup("oxiddealer") || ((bool) \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blShowNetPrice'));
    }

    public function loadUser($userName, $shopId){
        $this->loadAuthenticatedUser($userName, $shopId);
    }

}