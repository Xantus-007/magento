<?php


class Dbm_Share_Helper_Abuse extends Mage_Core_Helper_Abstract
{
    const TYPE_ELEMENT = 'element';
    const TYPE_COMMENT = 'comment';
    
    
    public function isTypeAllowed($type)
    {
        return in_array($type, $this->getTypes());
    }
    
    public function getTypes()
    {
        return array(self::TYPE_COMMENT, self::TYPE_ELEMENT);
    }
}