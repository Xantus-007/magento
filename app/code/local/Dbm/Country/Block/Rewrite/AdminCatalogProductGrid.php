<?php

/**
 * @deprecated
 * 
 * ByPass by TBT_Enhancedgrid_Block_Catalog_Product_Grid class
 */
class Dbm_Country_Block_Rewrite_AdminCatalogProductGrid extends Mage_Adminhtml_Block_Catalog_Product_Grid
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