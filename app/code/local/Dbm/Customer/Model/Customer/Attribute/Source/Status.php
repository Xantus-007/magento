<?php

class Dbm_Customer_Model_Customer_Attribute_Source_Status extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        return Mage::helper('dbm_customer')->getProfileStatusForSelect();
    }
}