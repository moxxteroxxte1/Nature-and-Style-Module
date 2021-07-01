<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


use OxidEsales\Eshop\Core\Registry;

class UserMain extends UserMain_parent
{

    public function login(){
        $config = Registry::getConfig();
        $oxid = Registry::getRequest()->getRequestEscapedParameter("oxid");

        $oUser = oxNew('oxuser');
        $oUser->load($oxid);
        $oUser->loadAuthenticatedUser($oUser->oxuser__oxusername->value, $config->getShopId());
        Registry::getSession()->setVariable('usr', $oxid);

        $sUrl = Registry::getConfig()->getShopHomeUrl();
        Registry::getUtils()->redirect($sUrl, true, 302);
    }

}