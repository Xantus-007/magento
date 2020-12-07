<?php

class Monbento_Newsletter_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{

    const XML_PATH_NEWSLETTER_EBOOK = 'newsletter/registrationincentive/ebook';
    
    public function subscribe($email)
    {
        $this->addToMailjet($email);
        $this->loadByEmail($email);
        $customerSession = Mage::getSingleton('customer/session');

        if(!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed = (Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
        $isOwnSubscribes = false;

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED || $this->getStatus() == self::STATUS_NOT_ACTIVE) {
            if ($isConfirmNeed === true) {
                // if user subscribes own login email - confirmation is not needed
                $ownerId = Mage::getModel('customer/customer')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($email)
                    ->getId();
                $isOwnSubscribes = ($customerSession->isLoggedIn() && $ownerId == $customerSession->getId());
                if ($isOwnSubscribes == true){
                    $this->setStatus(self::STATUS_SUBSCRIBED);
                }
                else {
                    $this->setStatus(self::STATUS_NOT_ACTIVE);
                }
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            $this->setSubscriberEmail($email);
        }

        if ($customerSession->isLoggedIn()) {
            $this->setStoreId($customerSession->getCustomer()->getStoreId());
            $this->setCustomerId($customerSession->getCustomerId());
        } else {
            $this->setStoreId(Mage::app()->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setIsStatusChanged(true);

        try {
            $this->save();
            Mage::dispatchEvent('dbm_gtm_newsletter_subscribe');
            if ($isConfirmNeed === true
                && $isOwnSubscribes === false
            ) {
                $this->sendConfirmationRequestEmail();
            } else {
                $this->sendConfirmationSuccessEmail();
            }

            return $this->getStatus();
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    public function sendConfirmationSuccessEmail() {
        return $this;
    }

    public function sendUnsubscriptionEmail() {
        return $this;
    }

    public function getEbook()
    {
        $ebookUrl = null;

        if($ebookFile = Mage::getStoreConfig(self::XML_PATH_NEWSLETTER_EBOOK)){
            $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'ebook/';
            $ebookUrl =  $mediaUrl . $ebookFile;
        }

        return $ebookUrl;
    }

    protected function addToMailjet($email)
    {
        $helper = Mage::helper('monbentonewsletter/data');
        $listID = Mage::getStoreConfig('newsletter/mailjet/contactslist');
        $helper->addContactToList($email, $listID);

        Mage::log("$email was added to #{$listID} list!",null,'mailjet.log',true);
    }
}

?>