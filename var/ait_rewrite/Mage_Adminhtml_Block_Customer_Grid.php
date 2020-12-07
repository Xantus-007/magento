<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */


class Altiplano_NoRegionNbOrder_Block_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{

		protected function _prepareCollection()
    {
      $collection = Mage::getResourceModel('customer/customer_collection')
        ->addNameToSelect()
        ->addAttributeToSelect('email')
        ->addAttributeToSelect('created_at')
        ->addAttributeToSelect('group_id')
        ->addAttributeToSelect('nb_order')
        ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
        ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
        ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
        ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
        ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

      $this->setCollection($collection);

      return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

		protected function _prepareColumns()
		{
			$this->addColumn('entity_id', array(
        'header'    => Mage::helper('customer')->__('ID'),
        'width'     => '50px',
        'index'     => 'entity_id',
        'type'  => 'number',
      ));

    	$this->addColumn('name', array(
        'header'    => Mage::helper('customer')->__('Name'),
        'index'     => 'name'
      ));

      $this->addColumn('email', array(
        'header'    => Mage::helper('customer')->__('Email'),
        'width'     => '150',
        'index'     => 'email'
      ));

      $this->addColumn('nb_order', array(
        'header'    => 'Nb commandes',
        'width'     => '90',
        'type' 		  => 'number',
        'align'     => 'center',
        'index' 		=> 'nb_order',
        'renderer'  => new Altiplano_NoRegionNbOrder_Block_Renderer_NbOrder(),
      ));

      $groups = Mage::getResourceModel('customer/group_collection')
        ->addFieldToFilter('customer_group_id', array('gt'=> 0))
        ->load()
        ->toOptionHash();

      $this->addColumn('group', array(
        'header'    =>  Mage::helper('customer')->__('Group'),
        'width'     =>  '100',
        'index'     =>  'group_id',
        'type'      =>  'options',
        'options'   =>  $groups,
      ));

      $this->addColumn('Telephone', array(
        'header'    => Mage::helper('customer')->__('Telephone'),
        'width'     => '100',
        'index'     => 'billing_telephone'
      ));

      $this->addColumn('billing_postcode', array(
        'header'    => Mage::helper('customer')->__('ZIP'),
        'width'     => '90',
        'index'     => 'billing_postcode',
      ));

      $this->addColumn('billing_country_id', array(
        'header'    => Mage::helper('customer')->__('Country'),
        'width'     => '100',
        'type'      => 'country',
        'index'     => 'billing_country_id',
      ));

      $this->addColumn('customer_since', array(
        'header'    => Mage::helper('customer')->__('Customer Since'),
        'type'      => 'datetime',
        'align'     => 'center',
        'index'     => 'created_at',
        'gmtoffset' => true
      ));

      if (!Mage::app()->isSingleStoreMode()) {
        $this->addColumn('website_id', array(
          'header'    => Mage::helper('customer')->__('Website'),
          'align'     => 'center',
          'width'     => '80px',
          'type'      => 'options',
          'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
          'index'     => 'website_id',
        ));
      }

      $this->addColumn('action',array(
        'header'    =>  Mage::helper('customer')->__('Action'),
        'width'     => '100',
        'type'      => 'action',
        'getter'    => 'getId',
        'actions'   => array(
          array(
            'caption'   => Mage::helper('customer')->__('Edit'),
            'url'       => array('base'=> '*/*/edit'),
            'field'     => 'id'
          )
        ),
        	'filter'    => false,
          'sortable'  => false,
          'index'     => 'stores',
          'is_system' => true,
        ));

      $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
      $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('XML'));

			return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
		}
}




class Aitoc_Aitpermissions_Block_Rewrite_AdminCustomerGrid extends Altiplano_NoRegionNbOrder_Block_Customer_Grid
{
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $collection = $this->getCollection();
        $collection->clear();
        $collection->addAttributeToSelect(array('profile_nickname', 'profile_status'));

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->removeColumn('nb_order');

        $this->addColumnAfter('profile_nickname', array(
            'header' => 'Pseudo club',
            'index' => 'profile_nickname',
        ), 'entity_id');
        $this->addColumnAfter('profile_status', array(
            'header' => 'Statut club',
            'index' => 'profile_status',
            'type' => 'options',
            'options' => $this->_getProfileStatus(),
        ), 'profile_nickname');
        $this->addColumnAfter('nb_order_by_dbm', array(
            'header'    => 'Nb commandes',
            'width'     => '90',
            'type'        => 'number',
            'align'     => 'center',
            'index'         => 'nb_order',
            'renderer'  => new Dbm_Share_Model_Adminhtml_Renderer_OrderCountByCustomer(),
        ), 'email');
        return parent::_prepareColumns();
    }

    protected function _getProfileStatus()
    {
        return Mage::helper('dbm_customer')->getProfileStatus('fr');
    }
}


/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminCustomerGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ IwMBSgrkTkoUqqmp('586b9907c393cb5b1896cc1344f3aa60'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Dbm_Customer_Block_Adminhtml_Customer_Grid extends Aitoc_Aitpermissions_Block_Rewrite_AdminCustomerGrid
{
	protected function _prepareColumns()
	{
		parent::_prepareColumns();

        $role = Mage::getSingleton('aitpermissions/role');

		if ($role->isPermissionsEnabled())
		{
            if (!Mage::helper('aitpermissions')->isShowingAllCustomers() &&
                isset($this->_columns['website_id']))
            {
                unset($this->_columns['website_id']);
                $allowedWebsiteIds = $role->getAllowedWebsiteIds();

                if (count($allowedWebsiteIds) > 1)
                {
                    $websiteFilter = array();
                    foreach ($allowedWebsiteIds as $allowedWebsiteId)
                    {
                        $website = Mage::getModel('core/website')->load($allowedWebsiteId);
                        $websiteFilter[$allowedWebsiteId] = $website->getData('name');
                    }

                    $this->addColumn('website_id', array(
                        'header' => Mage::helper('customer')->__('Website'),
                        'align' => 'center',
                        'width' => '80px',
                        'type' => 'options',
                        'options' => $websiteFilter,
                        'index' => 'website_id',
                    ));
                }
            }
		}
        
        return $this;
	}
} }

