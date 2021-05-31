<?php

namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

class TileCategoryAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    protected $_blAllowExtColumns = true;

    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxtitle', 'oxcategories', 1, 1, 0],
        ['oxid', 'oxcategories', 0, 0, 1],
    ]
    ];

    protected function _getQuery()
    {
        // active AJAX component
        $sGroupTable = $this->_getViewName('oxcategories');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $sSynchId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        if (!$sId) {
            $sQAdd = " from {$sGroupTable} where 1 ";
        } else {
            $sQAdd = " from oxobject2action, {$sGroupTable} where {$sGroupTable}.oxid=oxobject2action.oxobjectid " .
                " and oxobject2action.oxactionid = " . $oDb->quote($sId) .
                " and oxobject2action.oxclass = 'oxcategory' ";
        }

        if ($sSynchId && $sSynchId != $sId) {
            $sQAdd .= " and {$sGroupTable}.oxid not in ( select {$sGroupTable}.oxid " .
                "from oxobject2action, {$sGroupTable} where $sGroupTable.oxid=oxobject2action.oxobjectid " .
                " and oxobject2action.oxactionid = " . $oDb->quote($sSynchId) .
                " and oxobject2action.oxclass = 'oxcategory' ) ";
        }

        return $sQAdd;
    }

    protected function _addFilter($sQ) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sArtTable = $this->_getViewName('oxcategories');
        $sQ = parent::_addFilter($sQ);

        // display variants or not ?
        $sQ .= \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blVariantsSelection') ? ' group by ' . $sArtTable . '.oxid ' : '';

        return $sQ;
    }

    public function removeActionCategory()
    {
        $sActionId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        //$sActionId = $this->getConfig()->getConfigParam( 'oxid' );

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $oDb->Execute(
            'delete from oxobject2action '
            . 'where oxactionid = :oxactionid'
            . ' and oxclass = "oxcategory"',
            [':oxactionid' => $sActionId]
        );
    }

    public function setActionCategory()
    {
        $sCategoryId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxcategoryid');
        $sActionId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $oDb->Execute(
            'delete from oxobject2action '
            . 'where oxactionid = :oxactionid'
            . ' and oxclass = "oxcategory"',
            [':oxactionid' => $sActionId]
        );

        $oObject2Promotion = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
        $oObject2Promotion->init('oxobject2action');
        $oObject2Promotion->oxobject2action__oxactionid = new \OxidEsales\Eshop\Core\Field($sActionId);
        $oObject2Promotion->oxobject2action__oxobjectid = new \OxidEsales\Eshop\Core\Field($sCategoryId);
        $oObject2Promotion->oxobject2action__oxclass = new \OxidEsales\Eshop\Core\Field("oxcategory");
        $oObject2Promotion->save();
    }
}