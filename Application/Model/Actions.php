<?php


namespace NatureAndStyle\CoreModule\Application\Model;


use OxidEsales\Eshop\Core\DatabaseProvider;

class Actions extends Actions_parent
{

    public function getBannerTitle()
    {
        $oBannerObject = $this->getBannerObject();
        if($oBannerObject){
            return $this->getBannerObject()->getTitle();
        }elseif(isset($this->oxactions__oxtitle)){
            return $this->oxactions__oxtitle->value;
        }

        return null;
    }

    public function getLongDesc()
    {
        $oBannerObject = $this->getBannerObject();
        if(isset($this->oxactions__oxlongdesc)){
            return $this->oxactions__oxlongdesc->value;
        }elseif($oBannerObject){
            return $this->getBannerObject()->getLongDesc();
        }

        return null;
    }

    protected function fetchBannerObjectId()
    {
        $database = DatabaseProvider::getDb();

        return $database->getOne(
            'select oxobjectid from oxobject2action ' .
            'where oxactionid = :oxactionid',
            [
                ':oxactionid' => $this->getId(),
            ]
        );
    }

    protected function fetchBannerObjectClass(){
        $database = DatabaseProvider::getDb();

        return $database->getOne(
            'select oxclass from oxobject2action ' .
            'where oxactionid = :oxactionid',
            [
                ':oxactionid' => $this->getId(),
            ]
        );
    }

    public function getBannerObject()
    {
        $sObjId = $this->fetchBannerObjectId();
        $sObjClass = $this->fetchBannerObjectClass();

        if ($sObjId && $sObjClass) {
            $object = oxNew($sObjClass);

            if ($this->isAdmin() && is_a($object, '"OxidEsales\Eshop\Application\Model\Article')) {
                $object->setLanguage(\OxidEsales\Eshop\Core\Registry::getLang()->getEditLanguage());
            }

            if ($object->load($sObjId)) {
                return $object;
            }
        }

        return null;
    }

    public function getBannerLink()
    {
        $sUrl = null;

        $object = $this->getBannerObject();
        if($object){
            $sUrl = $object->getLink();
        }else if (isset($this->oxactions__oxlink) && $this->oxactions__oxlink->value) {
            /** @var \OxidEsales\Eshop\Core\UtilsUrl $oUtilsUlr */
            $oUtilsUlr = \OxidEsales\Eshop\Core\Registry::getUtilsUrl();
            $sUrl = $oUtilsUlr->addShopHost($this->oxactions__oxlink->value);
            $sUrl = $oUtilsUlr->processUrl($sUrl);
        }

        return $sUrl;
    }
}