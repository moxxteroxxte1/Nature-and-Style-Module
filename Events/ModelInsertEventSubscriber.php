<?php declare(strict_types=1);

namespace NatureAndStyle\CoreModule\Events;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelInsertEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelUpdateEvent;
use Symfony\Component\EventDispatcher\Event;

class ModelInsertEventSubscriber extends AbstractShopAwareEventSubscriber
{

    /** @var LoggerInterface */
    private $category = 'unique';

    public function logDatabaseActivity(Event $event)
    {
        $model = $event->getModel();

        if (is_a($model, "OxidEsales\Eshop\Application\Model\Article") && $model->isUnique() && $model->oxarticles__oxstockflag->value != 2)
        {
            $model->oxarticles__oxstockflag = new \OxidEsales\Eshop\Core\Field(2);
            $model->save();
        }else if(is_a($model, "OxidEsales\Eshop\Application\Model\Object2Category") && strpos($model->getCategoryId(), $this->category) !== false){
            $oxarticle  = oxNew('oxarticle');
            $oxarticle->load($model->getProductId());
            if($oxarticle->oxarticles__oxstockflag->value != 2){
                $oxarticle->oxarticles__oxstockflag = new \OxidEsales\Eshop\Core\Field(2);
                $oxarticle->save();
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [AfterModelUpdateEvent::NAME => 'logDatabaseActivity',
            AfterModelInsertEvent::NAME => 'logDatabaseActivity'];
    }
}