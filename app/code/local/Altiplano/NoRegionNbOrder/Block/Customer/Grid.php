<?php

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