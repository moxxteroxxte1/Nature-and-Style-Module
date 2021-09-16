<?php


namespace NatureAndStyle\CoreModule\Application\Controller;


use OxidEsales\Eshop\Application\Model\Action;

class ArticleListController extends ArticleListController_parent
{

    public function getBargainShortDescription()
    {
        $oAction = oxNew(Action::class);
        $oAction->load('oxbargain');
        return $oAction->oxactions__oxlongdesc->value;
    }

}