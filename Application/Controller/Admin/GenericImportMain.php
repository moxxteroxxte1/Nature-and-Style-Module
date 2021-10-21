<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


class GenericImportMain extends GenericImportMain_parent
{

    public function render()
    {
        if (Registry::getRequest()->getRequestEscapedParameter('sNavStep') == 2) {
            $this->_aViewData['blAutoFillCSV'] =  Registry::getRequest()->getRequestEscapedParameter('blAutoFillCSV');
        }
        return parent::render();
    }

}