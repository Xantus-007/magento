<?php
/**
 * Product:     Advanced Permissions
 * Package:     Aitoc_Aitpermissions_2.6.2_2.0.3_635589
 * Purchase ID: kvKHKGrR2nArLn9zDhP1NEqsGPa1BPw4KDqhieWQEX
 * Generated:   2013-07-16 07:25:55
 * File path:   app/code/local/Aitoc/Aitpermissions/Block/Rewrite/AdminhtmlWidgetInstanceGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitpermissions')){ CqyMSmZaIawcooki('ce56df52264cff20f4da342cc141eb6f'); ?><?php

/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlWidgetInstanceGrid
    extends Mage_Widget_Block_Adminhtml_Widget_Instance_Grid
{
    protected function _prepareCollection()
    {
        /* @var $collection Mage_Widget_Model_Mysql4_Widget_Instance_Collection */
        $collection = Mage::getModel('widget/widget_instance')->getCollection();
        $this->setCollection($collection);
        
        if ($this->getCollection())
        {
            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter))
            {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter))
            {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter))
            {
                $this->_setFilterValues($filter);
            }
            else if (0 !== sizeof($this->_defaultFilter))
            {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex())
            {
                $dir = (strtolower($dir) == 'desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $column = $this->_columns[$columnId]->getFilterIndex() ?
                    $this->_columns[$columnId]->getFilterIndex() : $this->_columns[$columnId]->getIndex();
                $this->getCollection()->setOrder($column, $dir);
            }

            $role = Mage::getSingleton('aitpermissions/role');

            if ($role->isPermissionsEnabled())
            {
                $this->getCollection()->addStoreFilter($role->getAllowedStoreviewIds(), false);
            }

            if (!$this->_isExport)
            {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }
} } 