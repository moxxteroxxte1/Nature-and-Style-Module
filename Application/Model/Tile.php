<?php


namespace NatureAndStyle\CoreModule\Application\Model;


use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Model\MultiLanguageModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsFile;
use OxidEsales\Eshop\Core\UtilsUrl;
use OxidEsales\Eshop\Core\UtilsView;

class Tile extends MultiLanguageModel
{
    protected $_sClassName = "oxactions";

    /**
     * Class constructor. Executes oxActions::init(), initiates parent constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init("oxactions");
    }

    /**
     * return time left until finished
     *
     * @return int
     */
    public function getTimeLeft()
    {
        $iNow = Registry::getUtilsDate()->getTime();
        $iFrom = strtotime($this->oxactions__oxactiveto->value);

        return $iFrom - $iNow;
    }

    public function getColor()
    {
        return $this->oxactions__oxcolor->value;
    }

    /**
     * return time left until start
     *
     * @return int
     */
    public function getTimeUntilStart()
    {
        $iNow = Registry::getUtilsDate()->getTime();
        $iFrom = strtotime($this->oxactions__oxactivefrom->value);

        return $iFrom - $iNow;
    }

    /**
     * start the promotion NOW!
     */
    public function start()
    {
        $this->oxactions__oxactivefrom = new Field(date('Y-m-d H:i:s', Registry::getUtilsDate()->getTime()));
        if ($this->oxactions__oxactiveto->value && ($this->oxactions__oxactiveto->value != '0000-00-00 00:00:00')) {
            $iNow = Registry::getUtilsDate()->getTime();
            $iTo = strtotime($this->oxactions__oxactiveto->value);
            if ($iNow > $iTo) {
                $this->oxactions__oxactiveto = new Field('0000-00-00 00:00:00');
            }
        }
        $this->save();
    }

    /**
     * stop the promotion NOW!
     */
    public function stop()
    {
        $this->oxactions__oxactiveto = new Field(date('Y-m-d H:i:s', Registry::getUtilsDate()->getTime()));
        $this->save();
    }

    /**
     * get long description, parsed through smarty
     *
     * @return string
     */
    public function getLongDesc()
    {
        /** @var UtilsView $oUtilsView */
        $oUtilsView = Registry::getUtilsView();
        return $oUtilsView->parseThroughSmarty($this->oxactions__oxlongdesc->getRawValue(), $this->getId() . $this->getLanguage(), null, true);
    }

    /**
     * return assigned banner article
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    public function getCategory()
    {
        $sCatId = $this->fetchCategory();

        if ($sCatId) {
            $oCategory = oxNew(Category::class);

            if ($oCategory->load($sCatId)) {
                return $oCategory;
            }
        }

        return null;
    }

    protected function fetchCategory()
    {
        $database = DatabaseProvider::getDb();

        $sCatId = $database->getOne(
            'select oxobjectid from oxobject2action ' .
            'where oxactionid = :oxactionid and oxclass = :oxclass',
            [
                ':oxactionid' => $this->getId(),
                ':oxclass' => 'oxcategory'
            ]
        );

        return $sCatId;
    }

    public function getPictureUrl()
    {
        if (isset($this->oxactions__oxpic) && $this->oxactions__oxpic->value) {
            $sPromoDir = Registry::getUtilsFile()->normalizeDir(UtilsFile::PROMO_PICTURE_DIR);

            return Registry::getConfig()->getPictureUrl($sPromoDir . $this->oxactions__oxpic->value, false);
        } elseif ($oCategory = $this->getCategory()) {
            return $oCategory->getPictureUrl();
        }
    }

    public function getTitle()
    {
        $sTitle = null;
        if ($oCategory = $this->getCategory()) {
            $sTitle = $oCategory->getTitle();
        }
        return $sTitle;
    }

    public function getLink()
    {
        $sUrl = null;
        if ($oCategory = $this->getCategory()) {
            $sUrl = $oCategory->getLink();
        }
        return $sUrl;
    }

    public function getShortDesc(){
        $sShortDesc = null;
        if($oCategory = $this->getCategory()){
            $sShortDesc = $oCategory->oxactions__oxlongdesc->value;
        }
        return $sShortDesc;
    }
}