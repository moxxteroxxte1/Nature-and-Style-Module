<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class User extends User_parent
{
    public function isPriceViewModeNetto()
    {
        return $this->inGroup("oxiddealer") || (Registry::getConfig()->getConfigParam('blShowNetPrice'));
    }

    public function getIslandSurcharge(){
        return $this->oxuser__oxislandsurcharge->value;
    }
}