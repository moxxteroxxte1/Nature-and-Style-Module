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
            $blAutoFillCSV =  Registry::getRequest()->getRequestEscapedParameter('blAutoFillCSV');
            $this->_aViewData['blAutoFillCSV'] = $blAutoFillCSV;
        }
        return parent::render();
    }

}