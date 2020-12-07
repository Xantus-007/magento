<?php

class Dbm_Share_Block_Adminhtml_Sales_Invoice_Grid extends Mage_Adminhtml_Block_Sales_Invoice_Grid
{

    protected function _prepareColumns()
    {        
        return parent::_prepareColumns();
        /*
        $this->addColumnAfter('fiscal_id', array(
            'header' => Mage::helper('sales')->__('Code fiscal'),
            'index' => 'fiscal_id',
            'type' => 'text',
            'sortable' => true
                ), 'remise_rule');
        
        $this->sortColumnsByOrder();
                
        return $this;
        */
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());        
        //$attributeId = Mage::getModel('eav/config')->getAttribute('customer', 'fiscal_id');
        $collection->getSelect()
                ->joinInner(
                        array('sfog' => 'sales_flat_order_grid'),
                        '`sfog`.`entity_id` = `main_table`.`order_id`',
                        null)
                /*->joinLeft(
                        'customer_entity_varchar', 
                        '`sfog`.`customer_id` = `customer_entity_varchar`.`entity_id` 
                            AND `customer_entity_varchar`.`attribute_id` = "' . $attributeId->getAttributeId() . '"',
                        array('fiscal_id' => 'value'))*/
        ;
        
        $collection
                ->addFilterToMap('entity_id', 'main_table.entity_id')
                ->addFilterToMap('increment_id', 'main_table.increment_id')
                ->addFilterToMap('created_at', 'main_table.created_at')
                ->addFilterToMap('grand_total', 'main_table.grand_total')
                ->addFilterToMap('billing_name', 'main_table.billing_name')
                ->addFilterToMap('state', 'main_table.state')
                ->addFilterToMap('store_id', 'main_table.store_id')
                ->addFilterToMap('base_grand_total', 'main_table.base_grand_total')
                ->addFilterToMap('order_id ', 'main_table.order_id ')
                ->addFilterToMap('store_currency_code', 'main_table.store_currency_code')
                ->addFilterToMap('order_currency_code', 'main_table.order_currency_code')
                ->addFilterToMap('base_currency_code', 'main_table.base_currency_code')
                ->addFilterToMap('global_currency_code', 'main_table.global_currency_code')
        //        ->addFilterToMap('fiscal_id', 'customer_entity_varchar.value')
        ;
        $this->setCollection($collection);
        $this->_preparePage();
        
        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        $dir = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
        $filter = $this->getParam($this->getVarNameFilter(), null);        
        if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }
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
            $collection->setOrder($column, $dir);
        }        
        if (!$this->_isExport) {
            $this->getCollection()->load();
            $this->_afterLoadCollection();
        }
        
        return $this;
    }
}

