<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class Discount extends Discount_parent
{
    public function isForBasketAmount($oBasket)
    {
        if ($this->oxdiscount__oxamountpackageunit->value) {
            $aBasketContents = $oBasket->getContents();
            foreach ($aBasketContents as $oBasketItem) {
                $oBasketArticle = $oBasketItem->getArticle(false);

                $blForBasketItem = ($this->oxdiscount__oxaddsumtype->value != 'itm' ?
                    $this->isForBasketItem($oBasketArticle) :
                    $this->isForBundleItem($oBasketArticle));

                if ($blForBasketItem) {
                    $dAmount = $oBasketItem->getAmount();
                    $dPackUnit = $oBasketArticle->getPackagingUnit();
                    if ($dPackUnit > 1 && ($dAmount % $dPackUnit == 0)) {
                        $oBasketItem->addDiscount($this->getAddSum(), $this->getAddSumType(), $this->getId());
                    }
                }
            }
            return false;
        }
        return parent::isForBasketAmount($oBasket);
    }

    public function getShortDesc()
    {
        return $this->oxdiscount__oxshortdesc->value;
    }

    public function fitPackagingUnit()
    {
        return $this->oxdiscount__oxamountpackageunit->value;
    }

    public function checkArticle($oArticle)
    {
        return ($this->isArticleAssigned($oArticle) || $this->isCategoriesAssigned($oArticle->getCategoryIds()));
    }

    protected function isArticleAssigned($oArticle)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sQ = "select 1
                from oxobject2discount
                where oxdiscountid = :oxdiscountid 
                    and oxtype = :oxtype ";
        $sQ .= $this->getProductCheckQuery($oArticle);
        $params = [
            ':oxdiscountid' => $this->oxdiscount__oxid->value,
            ':oxtype' => 'oxarticles'
        ];

        return $oDb->getOne($sQ, $params) ? true : false;
    }

    protected function getProductCheckQuery($oProduct)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        // check if this article is assigned
        if (($sParentId = $oProduct->getParentId())) {
            $sArticleId = " and ( oxobjectid = " . $oDb->quote($oProduct->getProductId()) . " or oxobjectid = " . $oDb->quote($sParentId) . " )";
        } else {
            $sArticleId = " and oxobjectid = " . $oDb->quote($oProduct->getProductId());
        }

        return $sArticleId;
    }

    protected function isCategoriesAssigned($aCategoryIds)
    {
        if (empty($aCategoryIds)) {
            return false;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sCategoryIds = "(" . implode(",", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aCategoryIds)) . ")";
        $sQ = "select 1
                from oxobject2discount
                where oxdiscountid = :oxdiscountid and oxobjectid in {$sCategoryIds} and oxtype = :oxtype";
        $params = [
            ':oxdiscountid' => $this->oxdiscount__oxid->value,
            ':oxtype' => 'oxcategories'
        ];

        return $oDb->getOne($sQ, $params) ? true : false;
    }
}

