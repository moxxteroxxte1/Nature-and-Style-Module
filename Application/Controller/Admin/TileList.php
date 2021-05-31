<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminListController;
use oxRegistry;

class TileList extends AdminListController
{
    protected $_sThisTemplate = 'tile_list.tpl';
    protected $_sListClass = 'oxactions';
    protected $_sDefSortField = 'oxtitle';

    public function render()
    {
        parent::render();

        // passing display type back to view
        $this->_aViewData["displaytype"] = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("displaytype");
        $this->_aViewData['mylist'] = $this->getItemList();

        return $this->_sThisTemplate;
    }

    /**
     * Adds active promotion check
     *
     * @param array  $aWhere  SQL condition array
     * @param string $sqlFull SQL query string
     *
     * @return $sQ
     * @deprecated underscore prefix violates PSR12, will be renamed to "prepareWhereQuery" in next major
     */
    protected function _prepareWhereQuery($aWhere, $sqlFull) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sQ = parent::_prepareWhereQuery($aWhere, $sqlFull);
        $sDisplayType = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('displaytype');
        $sTable = getViewName("oxactions");

        // searching for empty oxfolder fields
        if ($sDisplayType) {
            $sNow = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());

            switch ($sDisplayType) {
                case 1: // active
                    $sQ .= " and {$sTable}.oxactivefrom < '{$sNow}' and {$sTable}.oxactiveto > '{$sNow}' ";
                    break;
                case 2: // upcoming
                    $sQ .= " and {$sTable}.oxactivefrom > '{$sNow}' ";
                    break;
                case 3: // expired
                    $sQ .= " and {$sTable}.oxactiveto < '{$sNow}' and {$sTable}.oxactiveto != '0000-00-00 00:00:00' ";
                    break;
            }
        }
        $sQ .= " and {$sTable}.oxtype = 4";

        return $sQ;
    }

    protected function _buildSelectString($listObject = null)
    {
        return $listObject !== null ? $listObject->buildSelectString(null) . " and oxtype = 4" : "";
    }

    public function getItemList()
    {
        if ($this->_oList === null && $this->_sListClass) {
            $this->_oList = oxNew($this->_sListType);
            $this->_oList->clear();
            $this->_oList->init($this->_sListClass);

            $where = $this->buildWhere();

            $listObject = $this->_oList->getBaseObject();

            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('tabelle', $this->_sListClass);
            $this->_aViewData['listTable'] = getViewName($listObject->getCoreTableName());
            \OxidEsales\Eshop\Core\Registry::getConfig()->setGlobalParameter('ListCoreTable', $listObject->getCoreTableName());

            if ($listObject->isMultilang()) {
                // is the object multilingual?
                /** @var \OxidEsales\Eshop\Core\Model\MultiLanguageModel $listObject */
                $listObject->setLanguage(\OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage());

                if (isset($this->_blEmployMultilanguage)) {
                    $listObject->setEnableMultilang($this->_blEmployMultilanguage);
                }
            }

            $query = $this->_buildSelectString($listObject);
            $query = $this->_prepareWhereQuery($where, $query);
            $query = $this->_prepareOrderByQuery($query);
            $query = $this->_changeselect($query);

            // calculates count of list items
            $this->_calcListItemsCount($query);

            // setting current list position (page)
            $this->_setCurrentListPosition(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('jumppage'));

            // setting addition params for list: current list size
            $this->_oList->setSqlLimit($this->_iCurrListPos, $this->_getViewListSize());

            $this->_oList->selectString($query);
        }

        return $this->_oList;
    }
}