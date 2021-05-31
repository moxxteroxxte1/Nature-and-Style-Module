<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


class ActionsList extends ActionsList_parent
{
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
        $sQ .= " and {$sTable}.oxtype > 4";

        return $sQ;
    }
}