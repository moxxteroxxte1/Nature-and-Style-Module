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
    private $category = 'unique';

    public function onUpdate(Event $event)
    {
        $model = $event->getModel();

        if (is_a($model, "OxidEsales\Eshop\Application\Model\Article") && $model->isUnique() && $model->oxarticles__oxstockflag->value != 2) {
            $model->oxarticles__oxstockflag = new Field(2);
            $model->save();
        } else if (is_a($model, "OxidEsales\Eshop\Application\Model\Object2Category") && strpos($model->getCategoryId(), $this->category) !== false) {
            $oxarticle = oxNew('oxarticle');
            $oxarticle->load($model->getProductId());
            if ($oxarticle->oxarticles__oxstockflag->value != 2) {
                $oxarticle->oxarticles__oxstockflag = new Field(2);
                $oxarticle->save();
            }
        } else if (is_a($model, "OxidEsales\Eshop\Application\Model\Object2Group")){
            $sGroupId = $model->getFieldData('oxgroupsid');
            if($sGroupId != null && strpos($sGroupId, 'oxiddealer') !== false){
                $oxObject2Group = oxNew('oxobject2group');
                $sObjectId = $model->getFieldData('oxobjectid');
                $oxObject2Group->oxobject2group__oxobjectid = new Field($sObjectId);
                $oxObject2Group->oxobject2group__oxgroupsid = new Field('oxpricea');
                $oxObject2Group->save();
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
        }
    }

    public static function getSubscribedEvents()
    {
        return [AfterModelUpdateEvent::NAME => 'onUpdate',
            AfterModelInsertEvent::NAME => 'onInsert'];
    }
}