<?php

namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use \OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

class DynamicExportBaseController extends DynamicExportBaseController_parent
{

    public $sExportFileType = "csv";

}