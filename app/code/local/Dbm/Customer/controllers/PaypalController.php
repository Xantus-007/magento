<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Paypal').DS.'StandardController.php');

class Dbm_Customer_PaypalController extends Mage_Paypal_StandardController
{
    public function cancelAction()
    {
        parent::cancelAction();
        Mage::dispatchEvent('dbm_customer_order_cancel');
    }
    
    public function successAction()
    {
        parent::successAction();
        Mage::dispatchEvent('dbm_customer_order_success');
    }
}