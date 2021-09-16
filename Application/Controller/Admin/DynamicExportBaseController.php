<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


class DynamicExportBaseController extends DynamicExportBaseController_parent
{

    public string $sExportFileType = "csv";
    public $_sFilePath = "";

    public function __construct()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $myConfig->setConfigParam('blAdmin', true);
        $this->setAdminMode(true);

        if ($oShop = $this->getEditShop($myConfig->getShopId())) {
            // passing shop info
            $this->_sShopTitle = $oShop->oxshops__oxname->getRawValue();
        }

        $this->_sFilePath = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sShopDir') . "/" . $this->sExportPath . $this->sExportFileName . "." . $this->sExportFileType;
    }

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
        $sO2CView = $tableViewNameGenerator->getViewName('oxobject2category', $iExpLang);
        $sArticleTable = $tableViewNameGenerator->getViewName("oxarticles", $iExpLang);

        //$insertQuery = "insert into {$sHeapTable} select {$sArticleTable}.oxid from {$sArticleTable}, {$sO2CView} as oxobject2category where ";
        $insertQuery = "insert into {$sHeapTable} select * from {$sArticleTable}, {$sO2CView} as oxobject2category where ";
        $insertQuery .= $oArticle->getSqlActiveSnippet();

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

        // add minimum stock value
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blUseStock') && ($dMinStock = Registry::getRequest()->getRequestEscapedParameter("sExportMinStock"))) {
            $dMinStock = str_replace([";", " ", "/", "'"], "", $dMinStock);
            $insertQuery .= " and {$sArticleTable}.oxstock >= " . $oDB->quote($dMinStock);
        }

        $insertQuery .= " group by {$sArticleTable}.oxid";

        return $oDB->execute($insertQuery) ? true : false;
    }

}