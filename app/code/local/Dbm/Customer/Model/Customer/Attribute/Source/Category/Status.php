<?php

class Dbm_Customer_Model_Customer_Attribute_Source_Category_Status extends Dbm_Customer_Model_Customer_Attribute_Source_Status
{
    public function getAllOptions() {
        $options = Mage::helper('dbm_customer')->getProfileStatusForSelect();
        
        array_unshift($options, array(
            'label' => 'Tous',
            'value' => '-1'
        ));
        
        return $options;
    }
}