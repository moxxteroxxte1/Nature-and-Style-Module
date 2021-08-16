<?php declare(strict_types=1);

namespace NatureAndStyle\CoreModule\Events;

use OxidEsales\Eshop\Core\Exeption\DatabaseException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelInsertEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelUpdateEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelDeleteEvent;
use OxidEsales\Eshop\Core\DatabaseProvider;
use Symfony\Component\EventDispatcher\Event;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BasketChangedEvent;

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
            if ($model->isUnique() && $model->oxarticles__oxstockflag->value != 2) {
                $model->oxarticles__oxstockflag = new Field(2);
                $model->save();
            } elseif ($model->oxarticles__oxnew->value != 1 && $model->inCategory($this->category_new)) {
                try {
                    $query = "DELETE FROM `oxobject2category` WHERE OXOBJECTID = ? AND OXCATNID = 'new_articles'";
                    DatabaseProvider::getDb()->execute($query, [$model->getId()]);
                } catch (DatabaseException $exception) {
                    Registry::getUtilsView()->addErrorToDisplay($exception, false, true);
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

            $model->setId($model->oxarticles__oxartnum->value);
            $model->save();

            $oDb = DatabaseProvider::getDb();
            $sQ = 'delete from oxarticles where oxartnum=' . $oDb->quote($model->oxarticles__oxartnum->value) . " and oxartnum != oxid";
            $oDb->execute($sQ);

            $this->articleToCategory($model->oxarticles__oxartnum->value, $this->category_new);
        }
    }

    public function onFinalizedActivation(Event $event)
    {
        $logger = Registry::getLogger();
        $logger->info('1');
        $oCategory = oxNew('oxcategory');
        if (!$oCategory->load('new_articles')) {
            $logger->info('2');
            $oCategory->assign(array(
                "oxid" => 'new_articles',
                'oxtitle' => 'Unsere Neuheiten',
                'oxactive' => 1,
                "oxparentid" => "oxrootid",
                'oxsort' => 0
            ));
            $logger->info('3');
            $oCategory->save();
            $logger->info('4');
        }
    }

    private function articleToCategory($sAId, $sCid)
    {
        $oArticle = oxNew('oxarticle');
        $oArticle->load($sAId);
        $blInCategory = $oArticle->inCategory($this->category_new);

        $oDb = DatabaseProvider::getDb();
        $sQ = "SELECT count(OXID) FROM oxobject2category WHERE OXOBJECTID =" . $oDb->quote($sAId) . " and oxcatnid =" . $oDb->quote($sCid);
        $resultSet = $oDb->select($sQ);
        $allResults = $resultSet->fetchAll();
        $blDuplicateKey = $allResults[0][0] > 0;


        if ($oArticle->oxarticles__oxnew->value && !$blInCategory && !$blDuplicateKey) {
            $obj2cat = oxNew('oxobject2category');
            $obj2cat->init('oxobject2category');
            $obj2cat->assign(array(
                'oxcatnid' => $sCid,
                "oxobjectid" => $sAId
            ));
            $obj2cat->save();
        }
    }


    public static function getSubscribedEvents()
    {
        return [AfterModelUpdateEvent::NAME => 'onUpdate',
            AfterModelInsertEvent::NAME => 'onInsert',
            FinalizingModuleActivationEvent::NAME => 'onFinalizedActivation'];
    }
}