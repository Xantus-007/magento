<?php


class Dbm_Customer_Model_Notification extends Varien_Object
{
    public function init($date, $source, $label, $url) 
    {
        $this->setDate($date);
        $this->setSource($source);
        $this->setLabel($label);
        $this->setUrl($url);
    }
}