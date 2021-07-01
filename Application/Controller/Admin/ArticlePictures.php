<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Field;

class ArticlePictures extends ArticlePictures_parent
{

    public function updatePictureOrder()
    {
        $sOxId = $this->getEditObjectId();

        $oArticle = oxNew(Article::class);
        $oArticle->load($sOxId);

        $pictures = array();
        $orders = Registry::getRequest()->getRequestEscapedParameter("masterPicIndex");
        $orders = explode(",",$orders);
        $size = count($orders);

        for ($i = 0; $i < $size; $i += 2){
            array_push($pictures, array('id' => $orders[($i+1)], 'value' => $oArticle->{"oxarticles__oxpic".$orders[$i]}->value));
        }

        foreach ($pictures as $picture){
            $oArticle->{'oxarticles__oxpic'.$picture['id']} = new Field($picture['value']);
        }

        $oArticle->save();
    }
}