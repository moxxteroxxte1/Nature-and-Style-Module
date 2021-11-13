<?php

namespace NatureAndStyle\CoreModule\Application\Component;

use NatureAndStyle\CoreModule\Core\Email;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;

class UserComponent extends UserComponent_parent
{
    public function createUser()
    {
        if (false == $this->validateRegistrationOptin()) {
            //show error message on submit but not on page reload.
            if ($this->getRequestParameter('stoken')) {
                \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\UtilsView::class)->addErrorToDisplay('OEGDPROPTIN_CONFIRM_USER_REGISTRATION_OPTIN', false, true);
                \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\UtilsView::class)->addErrorToDisplay('OEGDPROPTIN_CONFIRM_USER_REGISTRATION_OPTIN', false, true, 'oegdproptin_userregistration');
            }
        } else {
            if (parent::createUser()) {
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
        return false;
    }


}