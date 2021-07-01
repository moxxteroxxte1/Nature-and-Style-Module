<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


use OxidEsales\Eshop\Core\Registry;

class UserMain extends UserMain_parent
{

    public function login()
    {
        $config = Registry::getConfig();
        $shopId = $config->getShopId();

        $oxid = Registry::getRequest()->getRequestEscapedParameter("oxid");
        $oUser = oxNew('oxuser');
        $oUser->load($oxid);
        $this->loadUser($oUser->oxuser__oxusername->value, $shopId);

        if (!$this->isLoaded()) {
            throw oxNew(UserException::class, 'ERROR_MESSAGE_USER_NOVALIDLOGIN');
        }

        Registry::getSession()->setVariable('usr', $oUser->oxuser__oxid->value);

        $sUrl = Registry::getConfig()->getShopHomeUrl();
        Registry::getUtils()->redirect($sUrl, true, 302);
    }

}