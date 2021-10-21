<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


use NatureAndStyle\CoreModule\Core\GenericImport\GenericImport;
use OxidEsales\Eshop\Core\NoJsValidator;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;

class GenericImportMain extends GenericImportMain_parent
{

    public function render()
    {
        $config = Registry::getConfig();

        $genericImport = oxNew(GenericImport::class);
        $this->_sCsvFilePath = null;

        $navigationStep = Registry::getRequest()->getRequestEscapedParameter('sNavStep');

        if (!$navigationStep) {
            $navigationStep = 1;
        } else {
            $navigationStep++;
        }

        $navigationStep = $this->checkErrors($navigationStep);

        if ($navigationStep == 1) {
            $this->_aViewData['sGiCsvFieldTerminator'] = Str::getStr()->htmlentities($this->getCsvFieldsTerminator());
            $this->_aViewData['sGiCsvFieldEncloser'] = Str::getStr()->htmlentities($this->getCsvFieldsEncolser());
        }

        if ($navigationStep == 2) {
            $noJsValidator = oxNew(NoJsValidator::class);
            //saving csv field terminator and encloser to config
            $terminator = Registry::getRequest()->getRequestEscapedParameter('sGiCsvFieldTerminator');
            if ($terminator && !$noJsValidator->isValid($terminator)) {
                $this->setErrorToView($terminator);
            } else {
                $this->_sStringTerminator = $terminator;
                $config->saveShopConfVar('str', 'sGiCsvFieldTerminator', $terminator);
            }

            $encloser = Registry::getRequest()->getRequestEscapedParameter('sGiCsvFieldEncloser');
            if ($encloser && !$noJsValidator->isValid($encloser)) {
                $this->setErrorToView($encloser);
            } else {
                $this->_sStringEncloser = $encloser;
                $config->saveShopConfVar('str', 'sGiCsvFieldEncloser', $encloser);
            }

            $type = Registry::getRequest()->getRequestEscapedParameter('sType');
            $importObject = $genericImport->getImportObject($type);
            $this->_aViewData['sType'] = $type;
            $this->_aViewData['sImportTable'] = $importObject->getBaseTableName();
            $this->_aViewData['aCsvFieldsList'] = $this->getCsvFieldsNames();
            $this->_aViewData['aDbFieldsList'] = $importObject->getFieldList();
        }

        if ($navigationStep == 3) {
            $csvFields = Registry::getRequest()->getRequestEscapedParameter('aCsvFields');
            $type = Registry::getRequest()->getRequestEscapedParameter('sType');

            $genericImport = oxNew(GenericImport::class);
            $genericImport->setImportType($type);
            $genericImport->setCsvFileFieldsOrder($csvFields);
            $genericImport->setCsvContainsHeader(Registry::getSession()->getVariable('blCsvContainsHeader'));

            $genericImport->importFile($this->getUploadedCsvFilePath());
            $this->_aViewData['iTotalRows'] = $genericImport->getImportedRowCount();

            //checking if errors occured during import
            $this->checkImportErrors($genericImport);

            //deleting uploaded csv file from temp dir
            $this->deleteCsvFile();

            //check if repeating import - then forsing first step
            if (Registry::getRequest()->getRequestEscapedParameter('iRepeatImport')) {
                $this->_aViewData['iRepeatImport'] = 1;
                $navigationStep = 1;
            }
        }

        if ($navigationStep == 1) {
            $this->_aViewData['aImportTables'] = $genericImport->getImportObjectsList();
            asort($this->_aViewData['aImportTables']);
            $this->resetUploadedCsvData();
        }

        $this->_aViewData['sNavStep'] = $navigationStep;

        return parent::render();
    }

    protected function checkErrors($iNavStep)
    {
        if ($iNavStep == 2) {
            if (!$this->getUploadedCsvFilePath()) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
                $oEx->setMessage('GENIMPORT_ERRORUPLOADINGFILE');
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'genimport');

                return 1;
            }
        }

        if ($iNavStep == 3) {
            $blIsEmpty = true;
            $aCsvFields = Registry::getRequest()->getRequestEscapedParameter('aCsvFields');
            foreach ($aCsvFields as $sValue) {
                if ($sValue) {
                    $blIsEmpty = false;
                    break;
                }
            }

            if ($blIsEmpty) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
                $oEx->setMessage('GENIMPORT_ERRORASSIGNINGFIELDS');
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'genimport');

                return 2;
            }
        }

        return $iNavStep;
    }

    protected function getCsvFieldsTerminator()
    {
        if ($this->_sStringTerminator === null) {
            $this->_sStringTerminator = $this->_sDefaultStringTerminator;
            if ($char = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sGiCsvFieldTerminator')) {
                $this->_sStringTerminator = $char;
            }
        }

        return $this->_sStringTerminator;
    }

    protected function getCsvFieldsEncolser()
    {
        if ($this->_sStringEncloser === null) {
            $this->_sStringEncloser = $this->_sDefaultStringEncloser;
            if ($char = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sGiCsvFieldEncloser')) {
                $this->_sStringEncloser = $char;
            }
        }

        return $this->_sStringEncloser;
    }

    private function setErrorToView($invalidData)
    {
        $error = oxNew(\OxidEsales\Eshop\Core\DisplayError::class);
        $error->setFormatParameters(htmlspecialchars($invalidData));
        $error->setMessage("SHOP_CONFIG_ERROR_INVALID_VALUE");
        \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($error);
    }

    protected function getCsvFieldsNames()
    {
        $blCsvContainsHeader = Registry::getRequest()->getRequestEscapedParameter('blContainsHeader');
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('blCsvContainsHeader', $blCsvContainsHeader);
        $this->getUploadedCsvFilePath();

        $aFirstRow = $this->getCsvFirstRow();

        if (!$blCsvContainsHeader) {
            $iIndex = 1;
            foreach ($aFirstRow as $sValue) {
                $aCsvFields[$iIndex] = 'Column ' . $iIndex++;
            }
        } else {
            foreach ($aFirstRow as $sKey => $sValue) {
                $aFirstRow[$sKey] = \OxidEsales\Eshop\Core\Str::getStr()->htmlentities($sValue);
            }

            $aCsvFields = $aFirstRow;
        }

        return $aCsvFields;
    }

    protected function checkImportErrors($oErpImport)
    {
        foreach ($oErpImport->getStatistics() as $aValue) {
            if (!$aValue ['r']) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
                $oEx->setMessage($aValue ['m']);
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'genimport');
            }
        }
    }

    protected function deleteCsvFile()
    {
        $sPath = $this->getUploadedCsvFilePath();
        if (is_file($sPath)) {
            @unlink($sPath);
        }
    }

    protected function resetUploadedCsvData()
    {
        $this->_sCsvFilePath = null;
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('sCsvFilePath', null);
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('blCsvContainsHeader', null);
    }
}