<?php

namespace NatureAndStyle\CoreModule\Application\Component;

use NatureAndStyle\CoreModule\Core\Email;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;

class UserComponent extends UserComponent_parent
{
    public function createUser()
    {
        if (parent::createUser() !== false) {
            $oUser = $this->getUser();

            $oxEMail = oxNew(Email::class);
            $oxEMail->sendRegisterEmailToOwner($oUser);

            $oUser->oxuser__oxactive = new Field(0);
            $oUser->save();
            $oUser->logout();

            $sUrl = Registry::getConfig()->getShopHomeUrl() . 'cl=content&tpl=user_inactive.tpl';
            Registry::getUtils()->redirect($sUrl, true, 302);
        }
    }

}