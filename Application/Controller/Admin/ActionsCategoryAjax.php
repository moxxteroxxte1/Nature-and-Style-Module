<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

class ActionsCategoryAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    protected $_blAllowExtColumns = true;

    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxid', 'oxcategories', 0, 0, 0],
        ['oxtitle', 'oxcategories', 1, 1, 0],
    ]
    ];

    protected function _getQuery() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sCategoryTable = $this->_getViewName('oxcategories');
        $sViewName = $this->_getViewName('oxobject2category');

        $sSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $sSynchSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        // category selected or not ?
        if (!$sSelId) {
            $sQAdd = " from $sCategoryTable where 1 ";
            $sQAdd .= $myConfig->getConfigParam('blVariantsSelection') ? '' : " and $sCategoryTable.oxparentid = '' ";
        } else {
            // selected category ?
            if ($sSynchSelId) {
                $blVariantsSelectionParameter = $myConfig->getConfigParam('blVariantsSelection');
                $sSqlIfTrue = " ({$sCategoryTable}.oxid=oxobject2category.oxobjectid " .
                    "or {$sCategoryTable}.oxparentid=oxobject2category.oxobjectid)";
                $sSqlIfFalse = " {$sCategoryTable}.oxid=oxobject2category.oxobjectid ";
                $sVariantSelection = $blVariantsSelectionParameter ? $sSqlIfTrue : $sSqlIfFalse;
                $sQAdd = " from {$sViewName} as oxobject2category left join {$sCategoryTable} on " . $sVariantSelection .
                    " where oxobject2category.oxcatnid = " . $oDb->quote($sSelId) . " ";
            }
        }
        // #1513C/#1826C - skip references, to not existing articles
        $sQAdd .= " and $sCategoryTable.oxid IS NOT NULL ";

        // skipping self from list
        $sQAdd .= " and $sCategoryTable.oxid != " . $oDb->quote($sSynchSelId) . " ";

        return $sQAdd;
    }

    protected function _addFilter($sQ) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sArtTable = $this->_getViewName('oxarticles');
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