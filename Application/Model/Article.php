<?php

namespace NatureAndStyle\CoreModule\Application\Model;

class Article extends Article_parent
{

    public function hasPackagingUnitDiscount(){
       $user = \OxidEsales\Eshop\Core\Registry::getSession()->getUser();
       if($user){
           $aDiscounts = new \OxidEsales\Eshop\Application\Model\DiscountList();
           $aDiscounts->getArticleDiscounts($this, $user);
           foreach ($aDiscounts as $oDiscount){
               if($oDiscount->oxdiscount__oxamountpackageunit->value){
                   return $oDiscount->oxdiscounts__oxtitle->value;
               }
           }
       }
       return false;
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
}