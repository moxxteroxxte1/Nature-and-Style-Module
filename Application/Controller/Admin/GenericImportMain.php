<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

class GenericImportMain extends GenericImportMain_parent
{

    public function render()
    {
        if (Registry::getRequest()->getRequestEscapedParameter('sNavStep') == 2) {
            Registry::getLogger()->warning("2");
            $this->_aViewData['blAutoFillCSV'] =  Registry::getRequest()->getRequestEscapedParameter('blAutoFillCSV');
        }
        return parent::render();
    }

}