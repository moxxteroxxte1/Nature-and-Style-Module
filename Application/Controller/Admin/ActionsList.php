<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;


class ActionsList extends ActionsList_parent
{
    protected function _prepareWhereQuery($aWhere, $sqlFull) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sQ = parent::_prepareWhereQuery($aWhere, $sqlFull);
        $sQ .= " and oxactions.oxtype < 4";
        return $sQ;
    }
}