<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminReportShopcartAbandonedGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ hpkmYyDZcZhCiirw('cc49e51db292e2f41e44bb24962f239a'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminReportShopcartAbandonedGrid
    extends Mage_Adminhtml_Block_Report_Shopcart_Abandoned_Grid
{
    protected function _prepareCollection()
    {
        $role = Mage::getSingleton('aitpermissions/role');
        
        $collection = Mage::getResourceModel('reports/quote_collection');
        
        $filter = $this->getParam($this->getVarNameFilter(), array());
        
        if ($filter) {
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);
        }
        
        if (!empty($data)) {
            $collection->prepareForAbandonedReport($this->_storeIds, $data);
        } else {
            $collection->prepareForAbandonedReport($this->_storeIds);
        }
          
        if ($role->isPermissionsEnabled())
        {
            if (!Mage::helper('aitpermissions')->isShowingAllCustomers())
            {
                $collection->getSelect()->joinLeft(array(
                        'customer' => Mage::getSingleton('core/resource')->getTableName('customer_entity')),
                        'customer.entity_id = main_table.customer_id',
                        array()
                    );
                
                $collection->addStoreFilter($role->getAllowedStoreviewIds());                
            }              
        }
        
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
} } 