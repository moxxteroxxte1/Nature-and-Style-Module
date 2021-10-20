<?php

namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use \OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class DynamicExportBaseController extends DynamicExportBaseController_parent
{

    public $sExportFileType = "csv";

    protected function insertArticles($sHeapTable, $sCatAdd)
    {
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $iExpLang = Registry::getRequest()->getRequestEscapedParameter("iExportLanguage");
        if (!isset($iExpLang)) {
            $iExpLang = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("iExportLanguage");
        }

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setLanguage($iExpLang);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName("oxarticles", $iExpLang);

        $insertQuery = "insert into {$sHeapTable} select {$sArticleTable}.oxid from {$sArticleTable} where ";

        if (!Registry::getRequest()->getRequestEscapedParameter("blExportVars")) {
            $insertQuery .= " and {$sArticleTable}.oxid = oxobject2category.oxobjectid and {$sArticleTable}.oxparentid = '' ";
        } else {
            $insertQuery .= " and ( {$sArticleTable}.oxid = oxobject2category.oxobjectid or {$sArticleTable}.oxparentid = oxobject2category.oxobjectid ) ";
        }

        $sSearchString = Registry::getRequest()->getRequestEscapedParameter("search");
        if (isset($sSearchString)) {
            $insertQuery .= "and ( {$sArticleTable}.OXTITLE like " . $oDB->quote("%{$sSearchString}%");
            $insertQuery .= " or {$sArticleTable}.OXSHORTDESC like " . $oDB->quote("%$sSearchString%");
            $insertQuery .= " or {$sArticleTable}.oxsearchkeys like " . $oDB->quote("%$sSearchString%") . " ) ";
        }

        if ($sCatAdd) {
            $insertQuery .= $sCatAdd;
        }

        return $oDB->execute($insertQuery) ? true : false;
    }

    protected function initArticle($sHeapTable, $iCnt, &$blContinue)
    {
        $oRs = $this->getDb()->selectLimit("select oxid from $sHeapTable", 1, $iCnt);
        if ($oRs != false && $oRs->count() > 0) {
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oArticle->setLoadParentData(true);

            $oArticle->setLanguage(\OxidEsales\Eshop\Core\Registry::getSession()->getVariable("iExportLanguage"));

            if ($oArticle->load($oRs->fields[0])) {
                // if article exists, do not stop export
                $blContinue = true;
                // check price
                $dMinPrice = Registry::getRequest()->getRequestEscapedParameter("sExportMinPrice");
                if (!isset($dMinPrice) || (isset($dMinPrice) && ($oArticle->getPrice()->getBruttoPrice() >= $dMinPrice))) {
                    //Saulius: variant title added
                    $sTitle = $oArticle->oxarticles__oxvarselect->value ? " " . $oArticle->oxarticles__oxvarselect->value : "";
                    $oArticle->oxarticles__oxtitle->setValue($oArticle->oxarticles__oxtitle->value . $sTitle);

                    $oArticle = $this->updateArticle($oArticle);

                    return $oArticle;
                }
            }
        }
    }

}