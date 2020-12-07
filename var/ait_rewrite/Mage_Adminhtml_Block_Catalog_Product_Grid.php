<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */

if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitquantitymanager')){ aTjTWiTZwjiyDTga('1b6b8065d7f5ed936d98ea1d7d3f757f');
/**
 * Multi-Location Inventory
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitquantitymanager
 * @version      2.1.9
 * @license:     EBR5kWF9n2SX6a9ZiEug4hNJ2bkUly0f6aLFfKrYjH
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitquantitymanager_Block_Rewrite_AdminCatalogProductGrid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    // override parent
    protected function _prepareCollection()
    {
        $store = $this->_getStore(); // aitoc code
        $iWebsiteId = $store->getWebsiteId(); // aitoc code
        $websites = Mage::getModel('core/website')->getCollection();
        
        if (!$iWebsiteId)
        {
            $iWebsiteId = Mage::helper('aitquantitymanager')->getHiddenWebsiteId();
        }
        
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->joinField('qty',
//                'cataloginventory/stock_staus',
                'aitquantitymanager/stock_item', // aitoc code
#                'aitquantitymanager/stock_status', // aitoc code
                'qty',
                'product_id=entity_id',
//                '{{table}}.stock_id=1',
                '{{table}}.stock_id=1 AND {{table}}.website_id = ' . $iWebsiteId, // aitoc code
                'left');
#d($iWebsiteId);

        foreach($websites as $website)
        {
            $collection->joinField('qty_'.$website->getCode(),
//                'cataloginventory/stock_staus',
                'aitquantitymanager/stock_item', // aitoc code
#                'aitquantitymanager/stock_status', // aitoc code
                'qty',
                'product_id=entity_id',
//                '{{table}}.stock_id=1',
                '{{table}}.stock_id=1 AND {{table}}.website_id = ' . $website->getId(), // aitoc code
                'left');

            $collection->joinField('status_'.$website->getCode(),
                'aitquantitymanager/stock_status', // aitoc code
                'stock_status',
                'product_id=entity_id',
                '{{table}}.stock_id=1 AND {{table}}.website_id = ' . $website->getId(),
                'left');
        }

        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }

        $collection->getSelect()->group('entity_id');
        
        $collection->getSelect()->distinct(true);
        $this->setCollection($collection);

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection(); // aitoc code
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }
} }



/**
 * @deprecated
 * 
 * ByPass by TBT_Enhancedgrid_Block_Catalog_Product_Grid class
 */
class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductGrid extends Aitoc_Aitquantitymanager_Block_Rewrite_AdminCatalogProductGrid
{
    
    /**
     * @deprecated
     * 
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $store = $this->_getStore(); // aitoc code
        //$iWebsiteId = $store->getWebsiteId(); // aitoc code
        /*
        if (!$iWebsiteId)
        {
            $iWebsiteId = Mage::helper('aitquantitymanager')->getHiddenWebsiteId();
        }*/
        
        $websites = Mage::getModel('core/website')->getCollection();
        
        $collection = $this->getCollection();
        if(!$collection)
        {
            $collection = new TBT_Enhancedgrid_Model_Resource_Eav_Mysql4_Product_Collection();
        }
        
        /*$collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
        ;
        
        foreach($websites as $website)
        {
            $collection->joinField('qty_'.$website->getCode(),
//                'cataloginventory/stock_staus',
                'aitquantitymanager/stock_item', // aitoc code
#                'aitquantitymanager/stock_status', // aitoc code
                'qty',
                'product_id=entity_id',
//                '{{table}}.stock_id=1',
                '{{table}}.stock_id=1 AND {{table}}.website_id = ' . $website->getId(), // aitoc code
                'left');
            
            $collection->joinField('status_'.$website->getCode(),
                'aitquantitymanager/stock_status', // aitoc code
                'stock_status',
                'product_id=entity_id',
                '{{table}}.stock_id=1 AND {{table}}.website_id = ' . $website->getId(), 
                'left');
        }*/
        
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            //$collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            //$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            //$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            //$collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }

        $this->setCollection($collection);
        
        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection(); // aitoc code
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }
    
    
    /**
     * @deprecated
     * 
     * @return \Dbm_Country_Block_Rewrite_AdminCatalogProductGrid
     */
    protected function _prepareColumns()
    {
        
        parent::_prepareColumns();
        $websites = Mage::getModel('core/website')->getCollection();
        /*
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
         */
        $this->addColumn( 'thumbnail', 
                array(
                    'header' => Mage::helper( 'catalog' )->__( 'Thumbnail' ), 
                    'type' => 'image', 
                    'width' => $imgWidth, 
                    'index' => 'thumbnail',
                    'renderer'=> 'TBT_Enhancedgrid_Block_Widget_Grid_Column_Renderer_Thumbnail'
                 ) );
        
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ));
        
        foreach($websites as $website)
        {
            $this->addColumnAfter('status_'.$website->getCode(),
                array(
                    'header'=> Mage::helper('catalog')->__('Status').' '.$website->getName(),
                    'width' => '50px',
                    'type'  => 'options',
                    'index' => 'status_'.$website->getCode(),
                    'options' => Mage::getModel( 'catalog/product_status' )->getOptionArray(),
            ), 'status');
            
            $this->addColumnAfter('qty_'.$website->getCode(),
                array(
                    'header'=> Mage::helper('catalog')->__('Qty').' '.$website->getName(),
                    'width' => '35px',
                    'type'  => 'number',
                    'index' => 'qty_'.$website->getCode(),
            ), 'qty');
        }
 
        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));

        return $this;
    }
}


/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminCatalogProductGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ qorySkjDCDqhwwah('8fc6dab3fb73eb6e430e31cd53002f9a'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Dbm_Country_Block_Rewrite_AdminCatalogProductGrid extends Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductGrid
{
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $role = Mage::getSingleton('aitpermissions/role');

        if (!$role->isPermissionsEnabled() || $role->isAllowedToDelete())
        {
            $this->getMassactionBlock()->addItem('delete', array(
                'label' => Mage::helper('catalog')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('catalog')->__('Are you sure?')
            ));
        }

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('catalog')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        $this->getMassactionBlock()->addItem('attributes', array(
            'label' => Mage::helper('catalog')->__('Update attributes'),
            'url' => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current' => true))
        ));

        if(!$role->isPermissionsEnabled() && Mage::helper('aitpermissions')->isShowProductOwner()) {
            $owners = Mage::getSingleton('aitpermissions/source_admins')->getOptionArray();

            array_unshift($owners, array('label' => '', 'value' => ''));
            $this->getMassactionBlock()->addItem('created_by', array(
                'label' => Mage::helper('catalog')->__('Set owner'),
                'url' => $this->getUrl('aitpermissions/adminhtml_catalogProduct/massOwner', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'created_by',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('catalog')->__('Owner name'),
                        'values' => $owners
                    )
                )
            ));

        }
        return $this;
    }
    
    protected function _toHtml()
    {
        $allowedWebisteIds = Mage::getSingleton('aitpermissions/role')->getAllowedWebsiteIds();
        if (count($allowedWebisteIds) <= 1)
        {
            unset($this->_columns['websites']);
        }
        return parent::_toHtml();
    }

    protected function _prepareCollection()
    {
        $this->_allowUpdateCollection = true;
        parent::_prepareCollection();
        $this->_allowUpdateCollection = false;
        return $this;
    }

    public function setCollection($collection)
    {
        if($this->_allowUpdateCollection && !Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled() && Mage::helper('aitpermissions')->isShowProductOwner()) {
            $collection->joinAttribute('created_by', 'catalog_product/created_by', 'entity_id', null, 'left');
        }
        return parent::setCollection($collection);
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        if(!Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled() && Mage::helper('aitpermissions')->isShowProductOwner()) {
            $this->addColumnAfter('created_by',
                array(
                    'header'=> Mage::helper('aitpermissions')->__('Owner'),
                    'width' => '70px',
                    'index' => 'created_by',
                    'type'  => 'options',
                    'options' => Mage::getSingleton('aitpermissions/source_admins')->getOptionArray(),
            ), 'status');
            $this->sortColumnsByOrder();
        }
        return $this;
    }
} }

