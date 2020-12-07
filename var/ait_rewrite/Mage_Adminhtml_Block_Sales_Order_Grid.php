<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */


class Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderGrid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    protected function _prepareColumns()
    {

        $this->addColumnAfter('cost', array(
            'header'    =>  Mage::helper('sales')->__('Cost'),
            'width'     =>  '100',
            'index'     =>  'cost',
            'type'      =>  'text',
            'renderer'  =>  new Dbm_Sales_Block_Adminhtml_Order_Renderer_Cost()
        ), 'grand_total');
 
        $this->addColumnsOrder('cost', 'grand_total');

        return parent::_prepareColumns();
    }
}



class Dbm_Expedition_Block_Adminhtml_Order_Grid extends Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderGrid
{

    protected function _prepareColumns()
    {

        $this->addColumnAfter('sender_admin_id', array(
            'header'    =>  Mage::helper('sales')->__('Expéditeur de commande'),
            'width'     =>  '100',
            'index'     =>  'sender_admin_id',
            'type'      =>  'text',
            'renderer'  =>  new Dbm_Expedition_Block_Adminhtml_Order_Renderer_Sender(),
            'filter_condition_callback' => array($this, 'senderAdminIdFilter')
        ), 'status');

        return parent::_prepareColumns();
    }
    
    protected function senderAdminIdFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $this->getCollection()->getSelect()
            ->joinLeft(array('admin_user' => 'admin_user'), 'admin_user.user_id = sof.sender_admin_id', array('firstname', 'lastname')) 
            ->where('sof.sender_admin_id <> 0')
            ->where("CONCAT(admin_user.firstname, ' ', admin_user.lastname) LIKE ?", "%$value%");

        return $this;
    }
}


/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class BoutikCircus_DeleteOrders_Block_Grid extends Dbm_Expedition_Block_Adminhtml_Order_Grid
{
	public function __construct() {
		parent::__construct ();
	}

    protected function _prepareMassaction()
    {
		parent::_prepareMassaction();
        $this->getMassactionBlock()->addItem('delete_order', array(
            'label'=> Mage::helper('sales')->__('Delete'),
		    'url'  => $this->getUrl('*/*/deleteorders', array('_current'=>true)),
        ));
        return $this;
    }
}



class Dbm_Share_Block_Adminhtml_Sales_Order_Grid extends BoutikCircus_DeleteOrders_Block_Grid
{

    protected function _prepareColumns()
    {        
        parent::_prepareColumns();
        
        $options = Mage::helper('dbm_share/order')->getOriginsForAdmin();
        $this->addColumnAfter('origin', array(
            'header' => Mage::helper('sales')->__('Origine'),
            'width' => '80px',
            'type' => 'options',
            'index' => 'origin',
            'align' => 'center',
            'renderer' => 'Dbm_Share_Model_Adminhtml_Renderer_Origin',
            'sortable' => true,
            'options' => $options
                ), 'website_id');
        
        $this->addColumnAfter('type_sav', array(
            'header' => Mage::helper('sales')->__('Type SAV'),
            'index' => 'type_sav',
            'type' => 'text',
            'sortable' => true
                ), 'fiscal_id');

        $this->addColumnAfter('frais_livraison_reel', array(
            'header' => Mage::helper('sales')->__('Frais livraison réels'),
            'index' => 'frais_livraison_reel',
            'type' => 'currency',
            'currency' => 'base_currency_code',
            'sortable' => true
                ), 'type_sav');
        
        $this->sortColumnsByOrder();
                
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $resource = Mage::getSingleton('core/resource');
        $tableName = $resource->getTableName('sales/order');

        $resourceAttribute = Mage::getSingleton('core/resource');
        $readConnection = $resourceAttribute->getConnection('code_read');

        $select = $collection->getSelect();

        $table = 'core/store';
        $cond = '`core/store`.store_id=`main_table`.`store_id`';
        $cols = array('parent_id' => 'website_id');
        $collection->getSelect()->joinLeft(array($table => $collection->getTable($table)), $cond, $cols);

        $table = 'core/website';
        $cond = '`core/website`.`website_id`=`core/store`.`website_id`';
        $cols = array('website_name' => 'name', 'website_id');
        $collection->getSelect()->joinLeft(array($table => $collection->getTable($table)), $cond, $cols);

        $select
                ->joinLeft(array('sof' => 'sales_flat_order'), 'sof.increment_id = main_table.increment_id', '')
                ->columns('sof.sender_admin_id')
                ->columns('sof.origin as origin')
                ->columns('sof.coupon_code as coupon_code')
                ->columns('sof.applied_rule_ids as applied_rule_ids')
                ->columns('sof.type_sav')
                ->columns('sof.frais_livraison_reel')                
        ;

        $this->setCollection($collection);

        $this->_preparePage();

        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        $dir = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
        $filter = $this->getParam($this->getVarNameFilter(), null);

        if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }

        $collection->getSelect()->group('main_table.entity_id');
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('store_id', 'main_table.store_id');
        $collection->addFilterToMap('grand_total', 'main_table.grand_total');
        $collection->addFilterToMap('base_grand_total', 'main_table.base_grand_total');
        $collection->addFilterToMap('origin', 'sof.origin');
        $collection->addFilterToMap('sender_admin_id', 'sof.sender_admin_id');
        
        if (is_string($filter)) {
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            $this->_setFilterValues($data);
        } else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        } else if (0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }

        if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
            $dir = (strtolower($dir) == 'desc') ? 'desc' : 'asc';
            $this->_columns[$columnId]->setDir($dir);
            $column = $this->_columns[$columnId]->getFilterIndex() ?
                    $this->_columns[$columnId]->getFilterIndex() : $this->_columns[$columnId]->getIndex();
            $this->getCollection()->setOrder($column, $dir);
        }

        if (!$this->_isExport) {
            $this->getCollection()->load();
            $this->_afterLoadCollection();
        }

        return $this;
    }

}


/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminSalesOrderGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ chmgYMarUriIppyo('4a1e2dfeb0d57b0a72eaee3b169127a2'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Dbm_Sales_Block_Adminhtml_Order_Grid extends Dbm_Share_Block_Adminhtml_Sales_Order_Grid
{
	protected function _prepareColumns()
	{
		parent::_prepareColumns();

        $role = Mage::getSingleton('aitpermissions/role');

		if ($role->isPermissionsEnabled())
		{
			$allowedStoreviews = $role->getAllowedStoreviewIds();
    		if (count($allowedStoreviews) <= 1 && isset($this->_columns['store_id']))
            {
                unset($this->_columns['store_id']);
            }
		}
        
		return $this;
	}
} }

