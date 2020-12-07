<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminReportReviewCustomerGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ ToBjSeEmQmqRwCgh('d884eb7cfc70661c89214313b6371960'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminReportReviewCustomerGrid
    extends Mage_Adminhtml_Block_Report_Review_Customer_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/review_customer_collection')->joinCustomers();

        if (!Mage::helper('aitpermissions')->isShowingAllCustomers())
        {
            $role = Mage::getSingleton('aitpermissions/role');
            
            if ($role->isPermissionsEnabled())
            {
                $collection->getSelect()->joinInner(
                    array('_table_customer' => Mage::getSingleton('core/resource')->getTableName('customer_entity')), 
                    ' _table_customer.entity_id = detail.customer_id ', 
                    array()
                    );

                $collection->addFieldToFilter('website_id', array('in' => $role->getAllowedWebsiteIds()));
            }
        }
        
        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
} } 