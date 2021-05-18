<?php

namespace NatureAndStyle\CoreModule\Application\Component;

class UserComponent extends UserComponent_parent
{
    public function createUser()
    {
        if(parent::createUser() !== false)
        {
            $oUser = $this->getUser();
            $oUser->oxuser__oxactive = new \OxidEsales\Eshop\Core\Field(0);
            $oUser->save();
            $oUser->logout();

            $sUrl = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopHomeUrl() . 'cl=content&tpl=user_inactive.tpl';
            \OxidEsales\Eshop\Core\Registry::getUtils()->redirect($sUrl, true, 302);
        }
    }
}