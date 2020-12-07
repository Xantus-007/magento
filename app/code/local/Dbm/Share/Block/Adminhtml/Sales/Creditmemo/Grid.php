<?php

class Dbm_Share_Block_Adminhtml_Sales_Creditmemo_Grid extends Mage_Adminhtml_Block_Sales_Creditmemo_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        $resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('sales/creditmemo');
        
        $cols = array(
            'sfc.tax_amount',
            'sfc.shipping_incl_tax',
            'sfc.subtotal_incl_tax',
            'sfop.method',
            'sfoa.country_id',
            'cs.website_id'
        );
        /*
        $table = 'core/store';
        $cond = '`core/store`.store_id=`main_table`.`store_id`';
        $cols = array('parent_id' => 'website_id');
        $collection->getSelect()->joinLeft(
            array(
                $table=>$collection->getTable($table)
            ), 
            $cond, 
            $cols
        );
        
        $table = 'core/website';
        $cond = '`core/website`.`website_id`=`core/store`.`website_id`';
        $cols = array('website_id' => 'website_id', 'website_id');
        $collection->getSelect()->joinLeft(array(
                $table=>$collection->getTable($table)
            ), 
            $cond,
            $cols
        );
        */
        
        $select = $collection->getSelect();
        $select->joinLeft(
                array('sfc' => 'sales_flat_creditmemo'),
                'sfc.entity_id = main_table.entity_id',
                ''
            )
            ->joinleft(
                array('sfop' => 'sales_flat_order_payment'),
                'main_table.order_id = sfop.parent_id',
                ''
            )
            ->joinLeft(
                array('sfoa' => 'sales_flat_order_address'),
                'main_table.order_id = sfoa.parent_id AND address_type="billing"' ,
                ''
            )
            ->joinLeft(
                array('cs' => 'core_store'),
                'main_table.store_id = cs.store_id',
                ''
            )
        ;
        
        foreach($cols as $col)
        {
            $select->columns($col);
        }
        
        //REWRITE PARENT::PARENT
        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }
            
            // Debug column created_at
            $collection->addFilterToMap('created_at', 'main_table.created_at');

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $column = $this->_columns[$columnId]->getFilterIndex() ?
                    $this->_columns[$columnId]->getFilterIndex() : $this->_columns[$columnId]->getIndex();
                $this->getCollection()->setOrder($column , $dir);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }
        //END REWRITE
        
        return $this;
    }
    
    protected function _prepareColumns()
    {
        $this->addColumnAfter('tax_amount', array(
            'header' => Mage::helper('sales')->__('incl. VAT'),
            'index' => 'tax_amount',
            'type' => 'currency',
            'currency' => 'order_currency_code'
        ), 'grand_total');
        
        $this->addColumnAfter('shipping_incl_tax', array(
            'header' => Mage::helper('sales')->__('incl. shipping'),
            'index' => 'shipping_incl_tax',
            'type' => 'currency',
            'currency' => 'order_currency_code'
        ), 'tax_amount');
        
        $this->addColumnAfter('subtotal_incl_tax', array(
            'header' => Mage::helper('sales')->__('products'),
            'index' => 'subtotal_incl_tax',
            'type' => 'currency',
            'currency' => 'order_currency_code'
        ), 'shipping_incl_tax');
        
        $this->addColumnAfter('method', array(
            'header' => Mage::helper('sales')->__('Payment method'),
            'index' => 'method',
            'type' => 'options',
            'options' => Mage::helper('dbm_share/payment')->getPaymentNamesFromCodes()
        ), 'subtotal_incl_tax');
        
        $this->addColumnAfter('country_id', array(
            'header' => Mage::helper('sales')->__('Country'),
            'index' => 'country_id',
            'type' => 'text',
        ), 'method');
        
        $this->addColumnAfter('website_id', array(
            'header' => Mage::helper('sales')->__('Stock'),
            'index' => 'website_id',
            'type' => 'options',
            'store_view'=> true,
            'display_deleted' => true,
            'options' => $this->_getWebsitesAsSelect(),
            'renderer' => new Mage_Adminhtml_Renderer_WebsiteRenderer(),
            //'filter_condition_callback' => array($this, '_storeFilter')
        ), 'country_id');
        
        return parent::_prepareColumns();
    }
    
    protected function _getWebsitesAsSelect()
    {
        $sites = Mage::getModel('core/website')->getCollection();
        $result = array();
        foreach($sites as $site)
        {
            $result[$site->getId()] = $site->getName();
        }
        
        return $result;
    }
}