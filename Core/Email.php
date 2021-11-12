<?php

namespace NatureAndStyle\CoreModule\Core;

class Email extends Email_parent
{

    public function sendRegisterEmailToOwner($user, $subject = null)
    {
        // add user defined stuff if there is any
        $user = $this->addUserRegisterEmail($user);

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


}