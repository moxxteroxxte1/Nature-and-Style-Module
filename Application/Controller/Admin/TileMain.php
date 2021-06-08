<?php

namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use stdClass;
use OxidEsales\Eshop\Application\Model\Actions;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\Eshop\Core\Field;

class TileMain extends AdminDetailsController
{
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();

        if ($this->isNewEditObject() !== true) {
            $oTile = oxNew(\NatureAndStyle\CoreModule\Application\Model\Tile::class);
            $oTile->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oTile->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                $oTile->loadInLang(key($oOtherLang), $soxId);
            }

            $this->_aViewData["edit"] = $oTile;

            // remove already created languages
            $aLang = array_diff(Registry::getLang()->getLanguageNames(), $oOtherLang);

            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        if (($oPromotion = $this->getViewDataElement("edit"))) {
            if ($oPromotion->oxactions__oxtype->value == 4) {
                if ($iAoc = Registry::getConfig()->getRequestParameter("oxpromotionaoc")) {
                    $sPopup = false;
                    switch ($iAoc) {
                        case 'category':
                            {
                                if ($oCategory = $oTile->getCategory()) {
                                    $this->_aViewData['tilecategory_id'] = $oCategory->oxcategories__oxid->value;
                                    $this->_aViewData['tilecategory_title'] = $oCategory->oxcategories__oxtitle->value;
                                }

                                $oTileCategoryAjax = oxNew(TileCategoryAjax::class);
                                $this->_aViewData['oxajax'] = $oTileCategoryAjax->getColumns();

                                return "tile_category.tpl";
                            }
                        case 'rmcolor':
                            {
                                $oTile->oxactions__oxcolor = new Field(null);
                            }

                    }
                }
            }
        }

        return "tile_main.tpl";
    }

    /**
     * Saves Promotions
     */
    public function save()
    {
        parent::save();

        $tile = oxNew(\NatureAndStyle\CoreModule\Application\Model\Tile::class);

        if ($this->isNewEditObject() !== true) {
            $tile->load($this->getEditObjectId());
        }

        $tile->assign($this->getTileFormData());
        $tile->setLanguage($this->_iEditLang);
        $tile = Registry::getUtilsFile()->processFiles($tile);
        $tile->save();

        $this->setEditObjectId($tile->getId());

    }

    /**
     * Saves changed selected action parameters in different language.
     */
    public function saveinnlang()
    {
        $this->save();
    }

    /**
     * Returns form data for Action.
     *
     * @return array
     */
    private function getTileFormData()
    {
        $request = oxNew(Request::class);
        $formData = $request->getRequestEscapedParameter("editval");
        $formData = $this->normalizeTileFormData($formData);

        return $formData;
    }

    /**
     * Normalizes form data for Action.
     *
     * @param array $formData
     *
     * @return  array
     */
    private function normalizeTileFormData($formData)
    {
        if ($this->isNewEditObject() === true) {
            $formData['oxactions__oxid'] = null;
        }

        if (!isset($formData['oxactions__oxactive']) || !$formData['oxactions__oxactive']) {
            $formData['oxactions__oxactive'] = 0;
        }

        return $formData;
    }
}