<?php declare(strict_types=1);

namespace NatureAndStyle\CoreModule\Events;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelInsertEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelUpdateEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelDeleteEvent;
use OxidEsales\Eshop\Core\DatabaseProvider;
use Symfony\Component\EventDispatcher\Event;

class ModelInsertEventSubscriber extends AbstractShopAwareEventSubscriber
{

    /** @var LoggerInterface */
    private $category_unique = 'unique';
    private $category_new = 'new_articles';

    public function onUpdate(Event $event)
    {
        $model = $event->getModel();

        if (is_a($model, "OxidEsales\Eshop\Application\Model\Article")) {
            $this->articleToCategory($model->oxarticles__oxid->value, $this->category_new);
            if($model->isUnique() && $model->oxarticles__oxstockflag->value != 2){
                $model->oxarticles__oxstockflag = new Field(2);
                $model->save();
            }elseif ($model->oxarticles__oxnew->value !== 1 && $model->inCategory($this->category_new)){
                try{
                    $query = "DELETE FROM `oxobject2category` WHERE OXOBJECTID = ? AND OXCATNID = 'new_articles'";
                    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query, [$model->getId()]);
                }catch (\OxidEsales\Eshop\Core\Exeption\DatabaseException $exception){
                    \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($exception, false, true);
                }
            }
        } else if (is_a($model, "OxidEsales\Eshop\Application\Model\Object2Category") && strpos($model->getCategoryId(), $this->category_unique) !== false) {
            $oxarticle = oxNew('oxarticle');
            $oxarticle->load($model->getProductId());
            if ($oxarticle->oxarticles__oxstockflag->value != 2) {
                $oxarticle->oxarticles__oxstockflag = new Field(2);
                $oxarticle->save();
            }
        }
    }

    public function onInsert(Event $event)
    {
        $model = $event->getModel();

        if (is_a($model, "OxidEsales\Eshop\Application\Model\Article")) {
            $sID = $model->getId();


            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oArticle->load($sID);
            $sArtNum = $oArticle->oxarticles__oxartnum->value;
            $oArticle->setId($sArtNum);

            $this->articleToCategory($sArtNum, $this->category_new);
        }
    }

    private function articleToCategory($sAId, $sCid){
        $oArticle = oxNew('oxarticle');
        $oArticle->load($sAId);
        $blInCategory = $oArticle->inCategory($this->category_new);
        if($oArticle->oxarticles__oxnew->value && !$blInCategory){
            $obj2cat = oxNew('oxobject2category');
            $obj2cat->init('oxobject2category');
            $obj2cat->assign(array(
                'oxcatnid'      => $sCid,
                "oxobjectid"    => $sAId
            ));
            $obj2cat->save();
        }
    }

    public static function getSubscribedEvents()
    {
        return [AfterModelUpdateEvent::NAME => 'onUpdate',
            AfterModelInsertEvent::NAME => 'onInsert'];
    }

}