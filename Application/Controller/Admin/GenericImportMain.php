<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

class GenericImportMain extends GenericImportMain_parent
{

    public function render()
    {
        if (Registry::getRequest()->getRequestEscapedParameter('sNavStep') == 2) {
            $blAutoFillCSV =  Registry::getRequest()->getRequestEscapedParameter('blAutoFillCSV');
            $this->_aViewData['blAutoFillCSV'] = $blAutoFillCSV;
        }
        return parent::render();
    }

}