<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Application\Model\DiscountList;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class Article extends Article_parent
{

    public function hasPackagingUnitDiscount(): array
    {
        $aIds = $this->discountIdQuery();
        $sResult = [];
        foreach ($aIds as $id){
            $oDiscount = oxNew('oxdiscount');
            $oDiscount->load($id);
            $sResult[] = $oDiscount;
        }
        return $sResult;
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

    private function discountIdQuery(){
        $aDiscountList = new DiscountList();
        $oBaseObject = $aDiscountList->getBaseObject();

        $sTable = $oBaseObject->getViewName();
        $sQ = "select $sTable.oxdiscountid from $sTable ";
        $sQ .= "where " . $oBaseObject->getSqlActiveSnippet() . ' ';


        // defining initial filter parameters
        $sUserId = null;
        $sGroupIds = null;
        $oUser = Registry::getSession()->getUser();
        $sCountryId = $aDiscountList->getCountryId($oUser);
        $oDb = DatabaseProvider::getDb();

        // checking for current session user which gives additional restrictions for user itself, users group and country
        if ($oUser) {
            // user ID
            $sUserId = $oUser->getId();

            // user group ids
            foreach ($oUser->getUserGroups() as $oGroup) {
                if ($sGroupIds) {
                    $sGroupIds .= ', ';
                }
                $sGroupIds .= $oDb->quote($oGroup->getId());
            }
        }

        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $sCountrySql = $sCountryId ? "EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxcountry' and oxobject2discount.OXOBJECTID=" . $oDb->quote($sCountryId) . ")" : '0';
        $sUserSql = $sUserId ? "EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxuser' and oxobject2discount.OXOBJECTID=" . $oDb->quote($sUserId) . ")" : '0';
        $sGroupSql = $sGroupIds ? "EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxgroups' and oxobject2discount.OXOBJECTID in ($sGroupIds) )" : '0';

        $sQ .= "and ($sTable.oxamountpackageunit = 1) and (
            select
                if(EXISTS(select 1 from oxobject2discount, $sCountryTable where $sCountryTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxcountry' LIMIT 1),
                        $sCountrySql,
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, $sUserTable where $sUserTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxuser' LIMIT 1),
                        $sUserSql,
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, $sGroupTable where $sGroupTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxgroups' LIMIT 1),
                        $sGroupSql,
                        1)
            )";

        $sQ .= " order by $sTable.oxsort ";

        $resultSet = $oDb->select($sQ);
        return $resultSet->fetchAll();
    }
}