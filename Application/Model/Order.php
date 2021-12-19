<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Application\Model\Payment as EshopPayment;
use OxidEsales\Eshop\Core\DatabaseProvider;

class Order extends Order_parent
{

    public function validateDelivery($oBasket)
    {
        if (Registry::getSession()->getVariable('hasNoShipSet')) {
            return;
        }
        return parent::validateDelivery($oBasket);
    }

    public function validateOrder($oBasket, $oUser)
    {
        // validating stock
        $iValidState = $this->validateStock($oBasket);

        if (!$iValidState) {
            // validating delivery
            $iValidState = $this->validateDelivery($oBasket);
        }

        if (!$iValidState) {
            // validating payment
            $iValidState = $this->validatePayment($oBasket, $oUser);
        }

        if (!$iValidState) {
            //0003110 validating delivery address, it is not be changed during checkout process
            $iValidState = $this->validateDeliveryAddress($oUser);
        }

        if (!$iValidState) {
            // validating minimum price
            $iValidState = $this->validateBasket($oBasket);
        }

        if (!$iValidState) {
            // validating vouchers
            $iValidState = $this->validateVouchers($oBasket);
        }

        return $iValidState;
    }

    private function isValidPayment($basket, $oUser = null)
    {
        $paymentId = $basket->getPaymentId();
        $paymentModel = oxNew(EshopPayment::class);
        $paymentModel->load($paymentId);

        $dynamicValues = $this->getDynamicValues();
        $shopId = Registry::getConfig()->getShopId();

        if (!$oUser) {
            $oUser = $this->getUser();
        }

        $session = Registry::getSession();

        return $paymentModel->isValidPayment(
            $dynamicValues,
            $shopId,
            $oUser,
            $basket->getPriceForPayment(),
            ($session->getVariable('hasNoShipSet') ? Registry::getConfig()->getConfigParam('nasdefaultshipset') : $session->getVariable('sShipSet'))
        );
    }

    public function validatePayment($oBasket, $oUser = null)
    {
        $paymentId = $oBasket->getPaymentId();

        if (!$this->isValidPaymentId($paymentId) || !$this->isValidPayment($oBasket, $oUser)) {
            return self::ORDER_STATE_INVALIDPAYMENT;
        }
    }

    private function isValidPaymentId($paymentId)
    {
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = DatabaseProvider::getMaster();

        $paymentModel = oxNew(EshopPayment::class);
        $tableName = $paymentModel->getViewName();

        $sql = "
            select
                1 
            from 
                {$tableName}
            where 
                {$tableName}.oxid = :oxid
                and {$paymentModel->getSqlActiveSnippet()}
        ";

        return (bool)$masterDb->getOne($sql, [
            ':oxid' => $paymentId
        ]);
    }

    private function getDynamicValues()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        $dynamicValues = $session->getVariable('dynvalue');

        if (!$dynamicValues) {
            $dynamicValues = Registry::getRequest()->getRequestParameter('dynvalue');
        }

        if (!$dynamicValues && $this->getPaymentType()) {
            $dynamicValues = $this->getDynamicValuesFromPaymentType();
        }

        return $dynamicValues;
    }

    private function getDynamicValuesFromPaymentType()
    {
        $dynamicValues = null;
        $dynamicValuesList = $this->getPaymentType()->getDynValues();

        if (is_array($dynamicValuesList)) {
            foreach ($dynamicValuesList as $value) {
                $dynamicValues[$value->name] = $value->value;
            }
        }

        return $dynamicValues;
    }
}