<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


use OxidEsales\Eshop\Core\Registry;

class ActionsMain extends ActionsMain_parent
{

    public function render()
    {
        $result = parent::render();

        $this->_aViewData["oxid"] = $this->getEditObjectId();
        $oPromotion = $this->getViewDataElement("edit");
        if ($oPromotion && $oPromotion->oxactions__oxtype->value == 3) {
            $iAoc = Registry::getConfig()->getRequestParameter("oxpromotionaoc");
            if ($iAoc && $iAoc == 'category') {
                if ($object = $oPromotion->getBannerObject()) {
                    $this->_aViewData['actionobject_id'] = $object->getId();
                    $this->_aViewData['actionobject_title'] = $object->getTitle();
                }
                $oActionsCategoryAjax = oxNew(ActionsCategoryAjax::class);
                $this->_aViewData['oxajax'] = $oActionsCategoryAjax->getColumns();

                return "actions_category.tpl";
            }
        }

        return $result;
    }

}