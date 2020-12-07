<?php


class Dbm_Map_Model_Api_V2 extends Mage_Api_Model_Resource_Abstract
{
    public function autocomplete($string)
    {
        $result = Mage::helper('dbm_map')->predict($string);
        
        return $result;
    }
}