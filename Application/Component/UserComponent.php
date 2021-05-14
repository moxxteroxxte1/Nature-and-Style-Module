<?php

namespace NatureAndStyle\CoreModule\Application\Component;

use OxidEsales\Eshop\Core\Registry;

class UserComponent extends UserComponent_parent
{
    public function createUser()
    {
        parent::createUser();

        if($this->validateRegistrationOptin()){
        $oUser = $this->getUser();
        $oUser->oxuser__oxactive = new \OxidEsales\Eshop\Core\Field(0);
        $oUser->save();
        $oUser->logout();

        $sUrl = Registry::getConfig()->getShopHomeUrl() . 'cl=content&tpl=user_inactive.tpl';
        Registry::getUtils()->redirect($sUrl, true, 302);
        }
    }
}