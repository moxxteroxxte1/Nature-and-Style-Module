<?php


namespace NatureAndStyle\CoreModule\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Field;

class ArticlePictures extends ArticlePictures_parent
{

    public function updatePictureOrder()
    {
        $myConfig = Registry::getConfig();

        $sOxId = $this->getEditObjectId();
        $orders = Registry::getRequest()->getRequestEscapedParameter("pictureOrder");

        $oArticle = oxNew(Article::class);
        $oArticle->load($sOxId);

        $pictures = array();

        foreach ($orders as $order) {
            array_push($pictures, array('id' => $order[1], 'value' => $this->getPicture($oArticle, $order[0])));
        }

        foreach ($pictures as $picture){
            $this->setPicture($oArticle, $picture['id'], $picture['value']);
        }

        $oArticle->save();
    }


    function getPicture($oArticle, $id)
    {
        switch ($id) {
            case 1:
                return $oArticle->oxarticles__oxpic1->value;
            case 2:
                return $oArticle->oxarticles__oxpic2->value;
            case 3:
                return $oArticle->oxarticles__oxpic3->value;
            case 4:
                return $oArticle->oxarticles__oxpic4->value;
            case 5:
                return $oArticle->oxarticles__oxpic5->value;
            case 6:
                return $oArticle->oxarticles__oxpic6->value;
            case 7:
                return $oArticle->oxarticles__oxpic7->value;
            case 8:
                return $oArticle->oxarticles__oxpic8->value;
            case 9:
                return $oArticle->oxarticles__oxpic9->value;
            case 10:
                return $oArticle->oxarticles__oxpic10->value;
            case 11:
                return $oArticle->oxarticles__oxpic11->value;
            case 12:
                return $oArticle->oxarticles__oxpic12->value;
            default:
                return false;
        }
    }

    function setPicture($oArticle, $id, $value)
    {
        switch ($id) {
            case 1:
                $oArticle->oxarticles__oxpic1 = new Field($value);
            case 2:
                $oArticle->oxarticles__oxpic2 = new Field($value);
            case 3:
                $oArticle->oxarticles__oxpic3 = new Field($value);
            case 4:
                $oArticle->oxarticles__oxpic4 = new Field($value);
            case 5:
                $oArticle->oxarticles__oxpic5 = new Field($value);
            case 6:
                $oArticle->oxarticles__oxpic6 = new Field($value);
            case 7:
                $oArticle->oxarticles__oxpic7 = new Field($value);
            case 8:
                $oArticle->oxarticles__oxpic8 = new Field($value);
            case 9:
                $oArticle->oxarticles__oxpic9 = new Field($value);
            case 10:
                $oArticle->oxarticles__oxpic10 = new Field($value);
            case 11:
                $oArticle->oxarticles__oxpic11 = new Field($value);
            case 12:
                $oArticle->oxarticles__oxpic12 = new Field($value);
            default:
                return false;
        }
    }
}