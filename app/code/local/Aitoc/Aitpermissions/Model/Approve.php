<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Model/Approve.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ piakYrejhjpqhhZq('c37861d535517b4fc7293c5d4428affd'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Model_Approve extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpermissions/approve', 'id');
    }

/*
 * @refactor
 * load single record by product id
 * rename to isProductApproved
 */
    public function isApproved($productId)
    {
        $collection = array();
        $collection = $this->getCollection()->loadByProductId($productId);
        foreach ($collection as $item)
        {
            return $item->getIsApproved();
        }

        return true;
    }

/*
 * @refactor
 * second parameter does not correspond to method name/purpose
 * split into separate methods approveProduct / disapproveProduct
 * remove magic number
 */
    public function approve($productId, $status = 1)
    {
        if ($status == Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus::STATUS_AWAITING)
        {
            $status = 0;
        }
        
        $collection = $this->getCollection()->loadByProductId($productId);
        if ($collection->getSize() > 0)
        {
            foreach ($collection as $item)
            {
                $item->setIsApproved($status)->save();
            }
        }
        else
        {
            $this->setProductId($productId)->setIsApproved($status)->save();
        }

        return true;
    }
} } 