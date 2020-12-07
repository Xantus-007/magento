<?php

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
