<?php

class Dbm_Share_Model_Mysql4_Photo_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('dbm_share/photo', 'dbm_share/photo');
    }

    public function addElementFilter(Dbm_Share_Model_Element $element)
    {
        $this->addFieldToFilter('id_element', $element->getId());

        return $this;
    }

    public function addMd5Filter($md5)
    {
        $this->addFieldToFilter('md5', $md5);
        return $this;
    }

    public function toApiArray()
    {
        $result = array();
        foreach($this as $photo)
        {
            $result[] = $photo->toApiArray();
        }

        return $result;
    }

}