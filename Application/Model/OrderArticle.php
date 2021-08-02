<?php


namespace NatureAndStyle\CoreModule\Application\Model;


class OrderArticle extends OrderArticle_parent
{

    public function getPackagingUnit(){
        return $this->oxorderarticles__oxpackagingunit->value;
    }

    public function getMinDelivery()
    {
        return $this->oxarticles__oxdeliverymin->value;
    }

}