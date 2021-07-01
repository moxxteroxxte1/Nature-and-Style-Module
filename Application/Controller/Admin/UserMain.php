<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


use OxidEsales\Eshop\Core\Registry;

class UserMain extends UserMain_parent
{

    public function login(){
        $oxid = Registry::getRequest()->getRequestEscapedParameter("oxid");
        Registry::getSession()->setVariable('usr', $oxid);

        $sUrl = Registry::getConfig()->getShopHomeUrl();
        Registry::getUtils()->redirect($sUrl, true, 302);
    }

}