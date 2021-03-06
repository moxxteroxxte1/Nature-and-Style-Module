<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\Eshop\Core\Registry;

/**
 * General export class.
 */
class GenericExportDo extends DynamicExportBaseController
{
    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo = "genExport_do";

    /**
     * Export ui class name
     *
     * @var string
     */
    public $sClassMain = "genExport_main";

    /**
     * Export file name
     *
     * @var string
     */
    public $sExportFileName = "genexport";

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = "dynbase_do.tpl";

    /**
     * Does Export line by line on position iCnt
     *
     * @param integer $iCnt export position
     *
     * @return bool
     */
    public function nextTick($iCnt)
    {
        $iExportedItems = $iCnt;
        $blContinue = false;
        if ($oArticle = $this->getOneArticle($iCnt, $blContinue)) {
            $myConfig = Registry::getConfig();
            $context = [
                "sCustomHeader" => Registry::getSession()->getVariable("sExportCustomHeader"),
                "linenr" => $iCnt,
                "article" => $oArticle,
                "spr" => $myConfig->getConfigParam('sCSVSign'),
                "encl" => $myConfig->getConfigParam('sGiCsvFieldEncloser')
            ];
            $context['oxEngineTemplateId'] = $this->getViewId();

            $this->write(
                $this->getRenderer()->renderTemplate(
                    "genexport.tpl",
                    $context
                )
            );

            return ++$iExportedItems;
        }

        return $blContinue;
    }

    /**
     * @return TemplateRendererInterface
     * @internal
     *
     */
    private function getRenderer()
    {
        return $this->getContainer()
            ->get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
    }

    /**
     * writes one line into open export file
     *
     * @param string $sLine exported line
     */
    public function write($sLine)
    {
        $sLine = $this->removeSID($sLine);

        $sLine = str_replace(["\r\n", "\n"], "", $sLine);
        $sLine = str_replace("<br>", "\n", $sLine);

        fwrite($this->fpFile, $sLine . "\n");
    }

    /**
     * Current view ID getter helps to identify navigation position.
     * Bypassing dynexportbase::getViewId
     *
     * @return string
     */
    public function getViewId()
    {
        return AdminController::getViewId();
    }
}