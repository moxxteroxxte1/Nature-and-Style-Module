<?php declare(strict_types=1);

namespace NatureAndStyle\CoreModule\Events;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelInsertEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelUpdateEvent;
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
            if($model->isUnique() && $model->oxarticles__oxstockflag->value != 2){
                $model->oxarticles__oxstockflag = new Field(2);
                $model->save();
            }elseif ($model->isNew() && !$model->inCategory($this->category_new)){
                $this->articleToCategory($model->oxarticles__oxid->value, $this->category_new);
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
            $sArtNum = $model->oxarticles__oxid->value;
            $model->oxarticles__oxartnum = new Field($sArtNum);
            $model->save();
            if($model->oxarticles__oxnew->value && !$model->inCategory('new_articles')){
                $this->articleToCategory($model->oxarticles__oxid->value, $this->category_new);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [AfterModelUpdateEvent::NAME => 'onUpdate',
            AfterModelInsertEvent::NAME => 'onInsert'];
    }

    private function articleToCategory($sAId, $sCid){
        $obj2cat = oxNew('oxobject2category');
        $obj2cat->init('object2category');
        $obj2cat->assign(array(
            'oxcatnid'      => $sCid,
            "oxobjectid"    => $sAId
        ));
        $obj2cat->save();
    }
}