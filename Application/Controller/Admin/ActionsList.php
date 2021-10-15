<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


class ActionsList extends ActionsList_parent
{
    protected function _prepareWhereQuery($aWhere, $sqlFull) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sTable = getViewName("oxactions");
        $sQ = parent::_prepareWhereQuery($aWhere, $sqlFull);
        $sQ .= " and {$sTable}.oxtype < 4";
        return $sQ;
    }
}