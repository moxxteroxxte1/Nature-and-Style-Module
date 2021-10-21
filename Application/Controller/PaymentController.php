<?php


namespace NatureAndStyle\CoreModule\Application\Controller;

use OxidEsales\Eshop\Application\Model\DeliverSetList;
use OxidEsales\Eshop\Application\Model\DeliverySet;
use OxidEsales\Eshop\Core\Registry;

class PaymentController extends PaymentController_parent
{

    public function render()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        if ($myConfig->getConfigParam('blPsBasketReservationEnabled')) {
            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $session->getBasketReservations()->renewExpiration();
        }

        parent::render();

        //if it happens that you are not in SSL
        //then forcing to HTTPS

        //but first checking maybe there were redirection already to prevent infinite redirections
        //due to possible buggy ssl detection on server
        $blAlreadyRedirected = Registry::getRequest()->getRequestEscapedParameter('sslredirect') == 'forced';

        if ($this->getIsOrderStep()) {
            //additional check if we really really have a user now
            //and the basket is not empty
            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $oBasket = $session->getBasket();
            $blPsBasketReservationEnabled = $myConfig->getConfigParam('blPsBasketReservationEnabled');
            if ($blPsBasketReservationEnabled && (!$oBasket || ($oBasket && !$oBasket->getProductsCount()))) {
                Registry::getUtils()->redirect($myConfig->getShopHomeUrl() . 'cl=basket', true, 302);
            }

            $oUser = $this->getUser();
            if (!$oUser && ($oBasket && $oBasket->getProductsCount() > 0)) {
                Registry::getUtils()->redirect($myConfig->getShopHomeUrl() . 'cl=basket', false, 302);
            } elseif (!$oBasket || !$oUser || ($oBasket && !$oBasket->getProductsCount())) {
                Registry::getUtils()->redirect($myConfig->getShopHomeUrl() . 'cl=start', false, 302);
            }
        }

        $sFncParameter = Registry::getRequest()->getRequestEscapedParameter('fnc');
        if ($myConfig->getCurrentShopURL() != $myConfig->getSSLShopURL() && !$blAlreadyRedirected && !$sFncParameter) {
            $sPayErrorParameter = Registry::getRequest()->getRequestEscapedParameter('payerror');
            $sPayErrorTextParameter = Registry::getRequest()->getRequestEscapedParameter('payerrortext');
            $shopSecureHomeURL = $myConfig->getShopSecureHomeURL();

            $sPayError = $sPayErrorParameter ? 'payerror=' . $sPayErrorParameter : '';
            $sPayErrorText = $sPayErrorTextParameter ? 'payerrortext=' . $sPayErrorTextParameter : '';
            $sRedirectURL = $shopSecureHomeURL . 'sslredirect=forced&cl=payment&' . $sPayError . "&" . $sPayErrorText;
            Registry::getUtils()->redirect($sRedirectURL, true, 302);
        }

        if (!$this->getAllSetsCnt()) {
            Registry::getSession()->setVariable('sShipSet', null);
        }

        $this->unsetPaymentErrors();

        return $this->_sThisTemplate;
    }

    public function changeshipping()
    {
        $session = Registry::getSession();

        $oBasket = $session->getBasket();
        $oBasket->setShipping(null);
        $oBasket->setSurcharge(false);
        $oBasket->setFindCheapest(false);
        $oBasket->onUpdate();
        $oBasket->setShipping('sShipSet', Registry::getRequest()->getRequestEscapedParameter('sShipSet'));

    }

    public function getAllSets()
    {
        $sActShipId = Registry::getSession()->getBasket()->getShippingId();
        $oActShip = oxNew(DeliverySet::class);
        if ($oActShip->load($sActShipId)) {
            return [$oActShip];
        }

        if ($this->_aAllSets === null) {
            $this->_aAllSets = false;

            if ($this->getPaymentList()) {
                return $this->_aAllSets;
            }
        }

        return $this->_aAllSets;
    }

}