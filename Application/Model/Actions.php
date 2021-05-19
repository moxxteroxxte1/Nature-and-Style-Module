<?php


namespace NatureAndStyle\CoreModule\Application\Model;


class Actions extends Actions_parent
{

    public function getBannerTitle()
    {
        if (isset($this->oxactions__oxtitle)) {
            return $this->oxactions__oxtitle->value;
        }else{
            return $this->getBannerObject()->getTitle();
        }

        return false;
    }

    public function getLongDesc()
    {
        if(isset($this->oxactions__oxlongdesc)){
            parent::getLongDesc();
        }else{
            $this->getBannerObject()->getLongDesc();
        }

        return false;
    }

    protected function fetchBannerObjectId()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $objectid = $database->getOne(
            'select oxobjectid from oxobject2action ' .
            'where oxactionid = :oxactionid',
            [
                ':oxactionid' => $this->getId(),
            ]
        );

        return $objectid;
    }

    protected function fetchBannerObjectClass(){
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $objectclass = $database->getOne(
            'select oxclass from oxobject2action ' .
            'where oxactionid = :oxactionid',
            [
                ':oxactionid' => $this->getId(),
            ]
        );

        return $objectclass;
    }

    public function getBannerObject()
    {
        $sObjId = $this->fetchBannerObjectId();
        $sObjClass = $this->fetchBannerObjectClass();

        $logger = \OxidEsales\Eshop\Core\Registry::getLogger();
        $logger->info($sObjId . " " . $sObjClass);

        if ($sObjId && $sObjClass) {
            $object = oxNew($sObjClass);

            if ($this->isAdmin() && is_a($object, '"OxidEsales\Eshop\Application\Model\Article')) {
                $object->setLanguage(\OxidEsales\Eshop\Core\Registry::getLang()->getEditLanguage());
            }

            if ($object->load($sObjId)) {
                $logger->info($object->getId());
                return $object;
            }
        }

        return false;
    }

    public function getBannerLink()
    {
        $sUrl = null;

        if (isset($this->oxactions__oxlink) && $this->oxactions__oxlink->value) {
            /** @var \OxidEsales\Eshop\Core\UtilsUrl $oUtilsUlr */
            $oUtilsUlr = \OxidEsales\Eshop\Core\Registry::getUtilsUrl();
            $sUrl = $oUtilsUlr->addShopHost($this->oxactions__oxlink->value);
            $sUrl = $oUtilsUlr->processUrl($sUrl);
        } else {
            $object = $this->getBannerObject();

            $logger = \OxidEsales\Eshop\Core\Registry::getLogger();
            $logger->info(get_class($object));

            if($object !== null){
                $sUrl = $object->getLink();
            }
        }

        return $sUrl;
    }
}