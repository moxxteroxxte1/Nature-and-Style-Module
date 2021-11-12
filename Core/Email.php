<?php

namespace NatureAndStyle\CoreModule\Core;

use OxidEsales\Eshop\Core\Output;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;

class Email extends Email_parent
{

    public function sendRegisterEmailToOwner($user, $subject = null)
    {
        // shop info
        $shop = $this->getShop();

        //set mail params (from, fromName, smtp )
        $this->setMailParams($shop);

        // create messages
        $renderer = $this->getRenderer();
        $this->setUser($user);

        // Process view data array through oxOutput processor
        $this->processViewArray();

        $this->setBody($renderer->renderTemplate($this->_sRegisterTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sRegisterTemplatePlain, $this->getViewData()));

        $this->setSubject(($subject !== null) ? $subject : $shop->oxshops__oxregistersubject->getRawValue());

        $fullName = $user->oxuser__oxfname->getRawValue() . " " . $user->oxuser__oxlname->getRawValue();

        $this->setRecipient($shop->oxshops__oxowneremail->value, $fullName);
        $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    protected function setMailParams($shop = null)
    {
        $this->clearMailer();

        if (!$shop) {
            $shop = $this->getShop();
        }

        $this->setFrom($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());
        $this->setSmtp($shop);
    }

    protected function processViewArray()
    {
        $outputProcessor = oxNew(Output::class);

        // processing assigned smarty variables
        $newArray = $outputProcessor->processViewArray($this->_aViewData, "oxemail");

        $this->_aViewData = array_merge($this->_aViewData, $newArray);
    }

    private function getRenderer()
    {
        $bridge = $this->getContainer()->get(TemplateRendererBridgeInterface::class);
        $bridge->setEngine($this->_getSmarty());

        return $bridge->getTemplateRenderer();
    }

    protected function clearMailer()
    {
        $this->clearAllRecipients();
        $this->clearReplyTos();
        $this->clearAttachments();

        $this->ErrorInfo = '';
    }

}