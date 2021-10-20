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

    protected function setSessionParams()
    {
        // reset it from session
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("sExportDelCost");
        $dDelCost = Registry::getRequest()->getRequestEscapedParameter("sExportDelCost");
        if (isset($dDelCost)) {
            $dDelCost = str_replace([";", " ", "/", "'"], "", $dDelCost);
            $dDelCost = str_replace(",", ".", $dDelCost);
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("sExportDelCost", $dDelCost);
        }

        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("sExportMinPrice");
        $dMinPrice = Registry::getRequest()->getRequestEscapedParameter("sExportMinPrice");
        if (isset($dMinPrice)) {
            $dMinPrice = str_replace([";", " ", "/", "'"], "", $dMinPrice);
            $dMinPrice = str_replace(",", ".", $dMinPrice);
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("sExportMinPrice", $dMinPrice);
        }

        // #827
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("sExportCampaign");
        $sCampaign = Registry::getRequest()->getRequestEscapedParameter("sExportCampaign");
        if (isset($sCampaign)) {
            $sCampaign = str_replace([";", " ", "/", "'"], "", $sCampaign);
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("sExportCampaign", $sCampaign);
        }

        // reset it from session
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("blAppendCatToCampaign");
        // now retrieve it from get or post.
        $blAppendCatToCampaign = Registry::getRequest()->getRequestEscapedParameter("blAppendCatToCampaign");
        if ($blAppendCatToCampaign) {
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("blAppendCatToCampaign", $blAppendCatToCampaign);
        }

        // reset it from session
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("iExportLanguage");
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("iExportLanguage", Registry::getRequest()->getRequestEscapedParameter("iExportLanguage"));

        //setting the custom header
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("sExportCustomHeader", Registry::getRequest()->getRequestEscapedParameter("sExportCustomHeader"));
    }

    protected function removeParentArticles($sHeapTable)
    {
        if (!(Registry::getRequest()->getRequestEscapedParameter("blExportMainVars"))) {
            $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');

            // we need to remove again parent articles so that we only have the variants itself
            $sQ = "select $sHeapTable.oxid from $sHeapTable, $sArticleTable where
                          $sHeapTable.oxid = $sArticleTable.oxparentid group by $sHeapTable.oxid";

            $oRs = $oDB->select($sQ);
            $sDel = "delete from $sHeapTable where oxid in ( ";
            $blSep = false;
            if ($oRs != false && $oRs->count() > 0) {
                while (!$oRs->EOF) {
                    if ($blSep) {
                        $sDel .= ",";
                    }
                    $sDel .= $oDB->quote($oRs->fields[0]);
                    $blSep = true;
                    $oRs->fetchRow();
                }
            }
            $sDel .= " )";
            $oDB->execute($sDel);
        }
    }

    protected function getCatAdd($aChosenCat)
    {
        $sCatAdd = null;
        if (is_array($aChosenCat) && count($aChosenCat)) {
            $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sCatAdd = " and ( ";
            $blSep = false;
            foreach ($aChosenCat as $sCat) {
                if ($blSep) {
                    $sCatAdd .= " or ";
                }
                $sCatAdd .= "oxobject2category.oxcatnid = " . $oDB->quote($sCat);
                $blSep = true;
            }
            $sCatAdd .= ")";
        }

        return $sCatAdd;
    }

    protected function createHeapTable($sHeapTable, $sTableCharset)
    {
        $blDone = false;
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "CREATE TABLE IF NOT EXISTS {$sHeapTable} ( `oxid` CHAR(32) NOT NULL default '' ) ENGINE=InnoDB {$sTableCharset}";
        if (($oDB->execute($sQ)) !== false) {
            $blDone = true;
            $oDB->execute("TRUNCATE TABLE {$sHeapTable}");
        }

        return $blDone;
    }

    protected function generateTableCharSet($sMysqlVersion)
    {
        $sTableCharset = "";

        //if MySQL >= 4.1.0 set charsets and collations
        if (version_compare($sMysqlVersion, '4.1.0', '>=') > 0) {
            $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
            $oRs = $oDB->select("SHOW FULL COLUMNS FROM `oxarticles` WHERE field like 'OXID'");
            if (isset($oRs->fields['Collation']) && ($sMysqlCollation = $oRs->fields['Collation'])) {
                $oRs = $oDB->select("SHOW COLLATION LIKE '{$sMysqlCollation}'");
                if (isset($oRs->fields['Charset']) && ($sMysqlCharacterSet = $oRs->fields['Charset'])) {
                    $sTableCharset = "DEFAULT CHARACTER SET {$sMysqlCharacterSet} COLLATE {$sMysqlCollation}";
                }
            }
        }

        return $sTableCharset;
    }

    protected function getHeapTableName()
    {
        // table name must not start with any digit
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        return "tmp_" . str_replace("0", "", md5($session->getId()));
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