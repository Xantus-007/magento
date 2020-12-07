<?php

class Dbm_TagManager_GanalyticsController extends Mage_Core_Controller_Front_Action
{
    public function addclientAction()
    {
        $clientId = $this->getRequest()->getParam('id');
        if ($clientId) {
            Mage::getSingleton('checkout/session')->setGaClientId($clientId);
        }

        exit;
    }
}
