<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Advancedrole.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ IwMBSgrkTkoUqqmp('5dfd587af29eb7b3d8180f087bcbc137'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Advancedrole extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpermissions/advancedrole');
    }

    public function getStoreviewIdsArray()
    {
        if (!$this->getStoreviewIds() || '0' == $this->getStoreviewIds())
        {
            return array();
        }
        return explode(',', $this->getStoreviewIds());
    }

    public function getCategoryIdsArray()
    {
        if (!$this->getCategoryIds() || '0' == $this->getCategoryIds())
        {
            return array();
        }
        return explode(',', $this->getCategoryIds());
    }

    public function canEditGlobalAttributes($roleId)
    {
        $recordCollection = $this->getCollection()->loadByRoleId($roleId);

        if ($recordCollection->getSize())
        {
            return (bool)$recordCollection->getFirstItem()->getCanEditGlobalAttr();
        }

        return true;
    }

    public function canEditOwnProductsOnly($roleId)
    {
        $recordCollection = $this->getCollection()->loadByRoleId($roleId);

        if ($recordCollection->getSize())
        {
            return (bool)$recordCollection->getFirstItem()->getCanEditOwnProductsOnly();
        }

        return true;
    }

    public function deleteRole($roleId)
    {
        $recordCollection = $this->getCollection()->loadByRoleId($roleId);

        if ($recordCollection->getSize())
        {
            foreach ($recordCollection as $record)
            {
                $record->delete();
            }
        }
    }
} } 