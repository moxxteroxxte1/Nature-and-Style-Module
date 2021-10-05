<?php

namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use \OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class DynamicExportBaseController extends DynamicExportBaseController_parent
{

    public $sExportFileType = "csv";


    protected function _insertArticles($sHeapTable, $sCatAdd) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oDB = DatabaseProvider::getDb();

        $iExpLang = Registry::getConfig()->getRequestParameter("iExportLanguage");
        if (!isset($iExpLang)) {
            $iExpLang = Registry::getSession()->getVariable("iExportLanguage");
        }

        $oArticle = oxNew(Article::class);
        $oArticle->setLanguage($iExpLang);

        $sO2CView = getViewName('oxobject2category', $iExpLang);
        $sArticleTable = getViewName("oxarticles", $iExpLang);

        $insertQuery = "insert into {$sHeapTable} select {$sArticleTable}.oxid from {$sArticleTable}, {$sO2CView} as oxobject2category where ";
        $insertQuery .= $oArticle->getSqlActiveSnippet();

        if (!Registry::getConfig()->getRequestParameter("blExportVars")) {
            $insertQuery .= " and {$sArticleTable}.oxid = oxobject2category.oxobjectid and {$sArticleTable}.oxparentid = '' ";
        } else {
            $insertQuery .= " and ( {$sArticleTable}.oxid = oxobject2category.oxobjectid or {$sArticleTable}.oxparentid = oxobject2category.oxobjectid ) ";
        }

        $sSearchString = Registry::getConfig()->getRequestParameter("search");
        if (isset($sSearchString)) {
            $insertQuery .= "and ( {$sArticleTable}.OXTITLE like " . $oDB->quote("%{$sSearchString}%");
            $insertQuery .= " or {$sArticleTable}.OXSHORTDESC like " . $oDB->quote("%$sSearchString%");
            $insertQuery .= " or {$sArticleTable}.oxsearchkeys like " . $oDB->quote("%$sSearchString%") . " ) ";
        }

        if ($sCatAdd) {
            $insertQuery .= $sCatAdd;
        }

        // add minimum stock value
        if (Registry::getConfig()->getConfigParam('blUseStock') && ($dMinStock = Registry::getConfig()->getRequestParameter("sExportMinStock"))) {
            $dMinStock = str_replace([";", " ", "/", "'"], "", $dMinStock);
            $insertQuery .= " and {$sArticleTable}.oxstock >= " . $oDB->quote($dMinStock);
        }

        $insertQuery .= " group by {$sArticleTable}.oxid";

        $logger = Registry::getLogger();
        $logger->error($insertQuery);

        return $oDB->execute($insertQuery) ? true : false;
    }
}