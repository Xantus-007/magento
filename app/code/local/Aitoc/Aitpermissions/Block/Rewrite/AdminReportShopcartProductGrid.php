<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminReportShopcartProductGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ CqyMSmZaIawcooki('bc20f94d02bf2f83fe04aa8cbdf0995f'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminReportShopcartProductGrid
    extends Mage_Adminhtml_Block_Report_Shopcart_Product_Grid
{
    protected function _prepareCollection()
    {
        $role = Mage::getSingleton('aitpermissions/role');
        
        $collection = Mage::getResourceModel('reports/quote_collection');
        if(version_compare(Mage::getVersion(), '1.6.0.0', '<'))
		{
		    $collection->prepareForProductsInCarts()
			    ->setSelectCountSqlType(Mage_Reports_Model_Mysql4_Quote_Collection::SELECT_COUNT_SQL_TYPE_CART);
        }
        else
        {
		    $collection->prepareForProductsInCarts()
			    ->setSelectCountSqlType(Mage_Reports_Model_Resource_Quote_Collection::SELECT_COUNT_SQL_TYPE_CART);
		}
		
        if ($role->isPermissionsEnabled())
        {
            if (!Mage::helper('aitpermissions')->isShowingAllProducts())
            {
                if ($role->isScopeStore())
                {
                    $collection->getSelect()->joinLeft(array(
                        'product_cat' => Mage::getSingleton('core/resource')->getTableName('catalog_category_product')),
                        'product_cat.product_id = e.entity_id',
                        array()
                    );

                    $collection->getSelect()->where(
                        ' product_cat.category_id in (' . join(',', $role->getAllowedCategoryIds()) . ')
                        or product_cat.category_id IS NULL '
                    );

                    $collection->getSelect()->distinct(true);
                }
                
                if ($role->isScopeWebsite())
                {
                    $collection->addStoreFilter($role->getAllowedStoreviewIds());
                }
            }           
        }

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
               
    }
    
    public function getRowUrl($row)
    {
        $role = Mage::getSingleton('aitpermissions/role');
        if ($role->isPermissionsEnabled())
        {
            $stores = $role->getAllowedStoreviewIds();
            return $this->getUrl('*/catalog_product/edit', array(
                'store'=>$stores[0], 
                'id'=>$row->getEntityId()
            ));
        }
        return parent::getRowUrl($row);
    }
} } 