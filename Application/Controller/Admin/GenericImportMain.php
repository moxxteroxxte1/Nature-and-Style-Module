<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

class GenericImportMain extends GenericImportMain_parent
{

    public function render()
    {
        $navigationStep = Registry::getRequest()->getRequestEscapedParameter('sNavStep');
        if (!$navigationStep) {
            $navigationStep = 1;
        } else {
            $navigationStep++;
        }

        if ($navigationStep == 2) {
            $this->_aViewData['blAutoFillCSV'] =  Registry::getRequest()->getRequestEscapedParameter('blAutoFillCSV');
            Registry::getLogger()->error($this->_aViewData['blAutoFillCSV']);
        }
        return parent::render();
    }

}