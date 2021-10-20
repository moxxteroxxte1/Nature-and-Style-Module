<?php

namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class DynamicExportBaseController extends DynamicExportBaseController_parent
{

    public $sExportFileType = "csv";

    public function prepareExport()
    {
        $oDB = DatabaseProvider::getDb();
        $sHeapTable = $this->getHeapTableName();

        // #1070 Saulius 2005.11.28
        // check mySQL version
        $oRs = $oDB->select("SHOW VARIABLES LIKE 'version'");
        $sTableCharset = $this->generateTableCharSet($oRs->fields[1]);

        // create heap table
        if (!($this->createHeapTable($sHeapTable, $sTableCharset))) {
            // error
            Registry::getUtils()->showMessageAndExit("Could not create HEAP Table {$sHeapTable}\n<br>");
        }

        $sCatAdd = $this->getCatAdd(Registry::getRequest()->getRequestEscapedParameter("acat"));
        if (!$this->insertArticles($sHeapTable, $sCatAdd)) {
            Registry::getUtils()->showMessageAndExit("Could not insert Articles in Table {$sHeapTable}\n<br>");
        }

        $this->removeParentArticles($sHeapTable);
        $this->setSessionParams();

        // get total cnt
        return $oDB->getOne("select count(*) from {$sHeapTable}");
    }

    protected function insertArticles($sHeapTable, $sCatAdd)
    {
        $oDB = DatabaseProvider::getDb();

        $iExpLang = Registry::getRequest()->getRequestEscapedParameter("iExportLanguage");
        if (!isset($iExpLang)) {
            $iExpLang = Registry::getSession()->getVariable("iExportLanguage");
        }

        $oArticle = oxNew(Article::class);
        $oArticle->setLanguage($iExpLang);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName("oxarticles", $iExpLang);

        $insertQuery = "insert into {$sHeapTable} select {$sArticleTable}.oxid from {$sArticleTable} where 1";

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

        $insertQuery .= " group by {$sArticleTable}.oxid";

        $logger = Registry::getLogger();
        $logger->info($insertQuery);

        return $oDB->execute($insertQuery) ? true : false;
    }

}