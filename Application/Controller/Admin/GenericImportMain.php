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

        $navigationStep = parent::checkErrors($navigationStep);

        if ($navigationStep == 1) {
            $this->_aViewData['sGiCsvFieldTerminator'] = Str::getStr()->htmlentities(parent::getCsvFieldsTerminator());
            $this->_aViewData['sGiCsvFieldEncloser'] = Str::getStr()->htmlentities(parent::getCsvFieldsEncolser());
        }

        if ($navigationStep == 2) {
            $noJsValidator = oxNew(NoJsValidator::class);
            //saving csv field terminator and encloser to config
            $terminator = Registry::getRequest()->getRequestEscapedParameter('sGiCsvFieldTerminator');
            if ($terminator && !$noJsValidator->isValid($terminator)) {
                parent::setErrorToView($terminator);
            } else {
                $this->_sStringTerminator = $terminator;
                $config->saveShopConfVar('str', 'sGiCsvFieldTerminator', $terminator);
            }

            $encloser = Registry::getRequest()->getRequestEscapedParameter('sGiCsvFieldEncloser');
            if ($encloser && !$noJsValidator->isValid($encloser)) {
                parent::setErrorToView($encloser);
            } else {
                $this->_sStringEncloser = $encloser;
                parent::saveShopConfVar('str', 'sGiCsvFieldEncloser', $encloser);
            }

            $type = Registry::getRequest()->getRequestEscapedParameter('sType');
            $importObject = $genericImport->getImportObject($type);
            $this->_aViewData['sType'] = $type;
            $this->_aViewData['sImportTable'] = $importObject->getBaseTableName();
            $this->_aViewData['aCsvFieldsList'] = parent::getCsvFieldsNames();
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
            parent::checkImportErrors($genericImport);

            //deleting uploaded csv file from temp dir
            parent::deleteCsvFile();

            //check if repeating import - then forsing first step
            if (Registry::getRequest()->getRequestEscapedParameter('iRepeatImport')) {
                $this->_aViewData['iRepeatImport'] = 1;
                $navigationStep = 1;
            }
        }

        if ($navigationStep == 1) {
            $this->_aViewData['aImportTables'] = $genericImport->getImportObjectsList();
            asort($this->_aViewData['aImportTables']);
            parent::resetUploadedCsvData();
        }

        $this->_aViewData['sNavStep'] = $navigationStep;

        return parent::render();
    }

}