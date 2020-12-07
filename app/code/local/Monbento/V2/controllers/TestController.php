<?php

class Monbento_V2_TestController extends Mage_Core_Controller_Front_Action
{
    public function translateAction()
    {
        
        echo $this->__('Continuez votre inscription');
        exit();
    }
    
    
    public function orderAction()
    {
        $order = Mage::getModel('sales/order')->load(50024);
        
        print_r($order->getData());
        exit();
    }
    
    public function testNamesAction()
    {
        $firstName = 'M Vincent de truc de meron Meron';
        $parts = explode(' ', $firstName);
        
        $_firstname = '';
        $_lastname = '';

        if(strlen($parts[0]) == 1)
        {
            $_firstname .= array_shift($parts).' ';
        }
        
        $_firstname .= array_shift($parts);
        
        $_lastname .= implode(' ', $parts);
        
        echo '<pre>'.$_firstname.' | '.$_lastname.'</pre>';
        exit();
    }
}