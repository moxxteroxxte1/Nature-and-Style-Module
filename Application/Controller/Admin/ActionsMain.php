<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


class ActionsMain extends ActionsMain_parent
{

    public function render()
    {
        $result = parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();

        if (($oPromotion = $this->getViewDataElement("edit"))) {
            if ($oPromotion->oxactions__oxtype->value == 3) {
                if ($iAoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxpromotionaoc")) {
                    $sPopup = false;
                    switch ($iAoc) {
                        case 'category':
                            // generating category tree for select list

                            if ($object = $oPromotion->getBannerObject()) {
                                $this->_aViewData['actionobject_id'] = $object->getId();
                                $this->_aViewData['actionobject_title'] = $object->getTitle();
                            }

                            $sPopup = 'actions_category';
                            break;
                    }

                    if ($sPopup) {
                        $oActionsArticleAjax = oxNew(ActionsCategoryAjax::class);
                        $this->_aViewData['oxajax'] = $oActionsArticleAjax->getColumns();

                        return "popups/{$sPopup}.tpl";
                    }
                }
            }
        }

        return $result;
    }

}