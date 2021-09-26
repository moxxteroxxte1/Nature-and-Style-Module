<?php


namespace NatureAndStyle\CoreModule\Application\Model;


use OxidEsales\Eshop\Core\DatabaseProvider;

class ContentList extends ContentList_parent
{

    const TYPE_SUB_CATEGORY = 4;

    public function loadCatMenues()
    {
        $this->load(self::TYPE_CATEGORY_MENU);
        $aArray = [];

        if ($this->count()) {
            foreach ($this as $oContent) {
                // add into category tree
                if (!isset($aArray[$oContent->getCategoryId()])) {
                    $aArray[$oContent->getCategoryId()] = [];
                }

                $aArray[$oContent->oxcontents__oxcatid->value][] = $oContent;
                $aSubCats = $this->loadSubCats($oContent->oxcontents__oxid->value);
                if(count($aSubCats) > 0){
                    $aArray[$oContent->oxcontents__oxcatid->value] = array_merge($aArray[$oContent->oxcontents__oxcatid->value],$aSubCats);
                }
            }
        }

        $this->_aArray = $aArray;
    }

    public function loadSubCats($CatId = null)
    {
        $oContentList = oxNew(ContentList::class);
        $oContentList->load(self::TYPE_SUB_CATEGORY, $CatId);
        $aArray = [];

        if ($oContentList->count()) {
            foreach ($oContentList as $oContent) {
                if (!isset($aArray[$oContent->getCategoryId()])) {
                    $aArray[$oContent->getCategoryId()] = [];
                }

                $aArray[$oContent->oxcontents__oxcatid->value] = $oContent;
            }
        }
        return $aArray;
    }

    protected function load($type, $CatId = null)
    {
        $data = $this->loadFromDb($type, $CatId);
        $this->assignArray($data);
    }

    protected function loadFromDb($iType, $CatId = null)
    {
        $sSql = $this->getSQLByType($iType, $CatId);
        $aData = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sSql);

        return $aData;
    }


    protected function getSQLByType($iType, $CatId = null)
    {
        $sSQLAdd = '';
        $oDb = DatabaseProvider::getDb();
        $sSQLType = " AND `oxtype` = " . $oDb->quote($iType);

        if ($iType == self::TYPE_SUB_CATEGORY && !is_null($CatId)) {
            $CatId = $oDb->quote($CatId);
            $sSQLAdd = "AND `oxcatid` = $CatId AND `oxsnippet` = '0'";
        }

        if ($iType == self::TYPE_CATEGORY_MENU) {
            $sSQLAdd = " ";
        }

        if ($iType == self::TYPE_SERVICE_LIST) {
            $sIdents = implode(", ", DatabaseProvider::getDb()->quoteArray($this->getServiceKeys()));
            $sSQLAdd = " AND OXLOADID IN (" . $sIdents . ")";
            $sSQLType = '';
        }
        $sViewName = $this->getBaseObject()->getViewName();
        $sSql = "SELECT * FROM {$sViewName} WHERE `oxactive` = '1' $sSQLType AND `oxshopid` = " . $oDb->quote($this->_sShopID) . " $sSQLAdd ORDER BY `oxloadid`";

        return $sSql;
    }

}