<?php

class Dbm_Share_Model_Mysql4_Photo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('dbm_share/photo', 'id');
    }

    /**
     * Deleting photo file after db deletion
     * @param Mage_Core_Model_Abstract $object
     * @return Dbm_Share_Model_Mysql4_Photo
     */
    public function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        //Delete file
        $filePath = Mage::helper('dbm_share')->getPhotoDir($object->getFilename()).$object->getFilename();

        if(file_exists($filePath))
        {
            @unlink($filePath);
            Mage::helper('dbm_share')->cleanPhotoPath($object->getFilename());
        }

        return parent::_afterDelete($object);
    }
}