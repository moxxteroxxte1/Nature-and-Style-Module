<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class Discount extends Discount_parent
{
    public function isForBasketAmount($oBasket)
    {
        $logger = Registry::getLogger();

        if ($this->oxdiscount__oxamountpackageunit->value) {
            $aBasketContents = $oBasket->getContents();
            foreach ($aBasketContents as $oBasketItem) {
                $oBasketArticle = $oBasketItem->getArticle(false);

                $blForBasketItem = ($this->oxdiscount__oxaddsumtype->value != 'itm' ?
                    !is_null($this->isForBasketItem($oBasketArticle)):
                    $this->isForBundleItem($oBasketArticle));

                if ($blForBasketItem) {
                    $dAmount = $oBasketItem->getAmount();
                    $dPackUnit = $oBasketArticle->getPackagingUnit();
                    $logger->info("$dPackUnit: " . $dPackUnit . " $dAmount: " . $dAmount . " ($dAmount % $dPackUnit == 0): " . ($dAmount % $dPackUnit == 0));
                    if ($dPackUnit > 1 && ($dAmount % $dPackUnit == 0)) {
                        $logger->info($blForBasketItem);
                        $oBasketItem->addDiscount($this->getAddSum(), $this->getAddSumType(), $this->getId());
                    }
                }
            }
            return false;
        }
        return parent::isForBasketAmount($oBasket);
    }


    public function isForBasketItem($oArticle)
    {
        if ($this->oxdiscount__oxamount->value == 0 && $this->oxdiscount__oxprice->value == 0 && $this->oxdiscount__oxamountpackageunit->value == 0) {
            return false;
        }

        // skipping bundle discounts
        if ($this->oxdiscount__oxaddsumtype->value == 'itm') {
            return false;
        }

        $oDb = DatabaseProvider::getDb();

        // check if this article is assigned
        $sQ = "select 1 from oxobject2discount 
            where oxdiscountid = :oxdiscountid and oxtype = :oxtype ";
        $sQ .= $this->getProductCheckQuery($oArticle);
        $params = [
            ':oxdiscountid' => $this->oxdiscount__oxid->value,
            ':oxtype' => 'oxarticles'
        ];

        if (!($blOk = (bool)$oDb->getOne($sQ, $params))) {
            // checking article category
            $blOk = $this->checkForArticleCategories($oArticle);
        }

        return $blOk;
    }

    protected function checkForArticleCategories($oArticle)
    {
        // check if article is in some assigned category
        $aCatIds = $oArticle->getCategoryIds();
        if (!$aCatIds || !count($aCatIds)) {
            // no categories are set for article, so no discounts from categories..
            return false;
        }

        $sCatIds = "(" . implode(",", DatabaseProvider::getDb()->quoteArray($aCatIds)) . ")";

        $oDb = DatabaseProvider::getDb();
        // getOne appends limit 1, so this one should be fast enough
        $sQ = "select oxobjectid from oxobject2discount 
            where oxdiscountid = :oxdiscountid 
                and oxobjectid in $sCatIds 
                and oxtype = :oxtype";

        return $oDb->getOne($sQ, [
            ':oxdiscountid' => $this->oxdiscount__oxid->value,
            ':oxtype' => 'oxcategories'
        ]);
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
        return ($this->_isArticleAssigned($oArticle) || $this->_isCategoriesAssigned($oArticle->getCategoryIds()));
    }
}

