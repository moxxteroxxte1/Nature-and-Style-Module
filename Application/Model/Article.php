<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use NatureAndStyle\CoreModule\Application\Model\DiscountList;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class Article extends Article_parent
{

    public function getDiscounts()
    {
        $oDeliveryList = oxNew(DiscountList::class);
        return $oDeliveryList->getPotenzialDiscounts($this, $this->getUser());
    }

    public function getTitle()
    {
        return $this->oxarticles__oxtitle->value;
    }

    public function getPackagingUnit(): int
    {
        return $this->oxarticles__oxpackagingunit->value;
    }

    public function isUnique(): bool
    {
        return $this->oxarticles__oxunique->value || $this->_uniqueCategory();
    }

    public function isNew(): bool
    {
        return $this->oxarticles__oxnew->value;
    }

    private function _uniqueCategory(): bool
    {
        foreach ($this->getCategoryIds() as $id) {
            if (strpos($id, 'unique') !== false) {
                return true;
            }
        }
        return false;
    }

    public function getMinDelivery()
    {
        return $this->oxarticles__oxdeliverymin->value;
    }

    public function hasMinDelivery()
    {
        $sMinDel = $this->getMinDelivery();
        return ($sMinDel != null && $sMinDel != '----');
    }

    public function isCarrierShipping()
    {
        if ($this->hasMinDelivery()) {
            $oDeliver = oxNew(Delivery::class);
            $oDeliver->load($this->getMinDelivery());
            return $oDeliver->isMarkedShipping();
        }
    }

    public function isBulkyGood()
    {
        $this->oxarticles__oxbulkygood->value;
    }

    public function getBulkyGoodMultiplier()
    {
        $this->oxarticles__oxbulkygoodmultiplier->value;
    }

    public function assign($dbRecord)
    {
        if (!is_array($dbRecord)) {
            return;
        }

        parent::assign($dbRecord);

        $key = 'OXLONGDESC';
        if(isset($dbRecord[$key])){
            parent::setArticleLongDesc($dbRecord[$key]);
            unset($dbRecord[$key]);
        }
    }
}