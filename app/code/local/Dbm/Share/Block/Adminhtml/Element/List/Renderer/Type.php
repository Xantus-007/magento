<?php

class Dbm_Share_Block_Adminhtml_Element_List_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $prettyType = ucwords(Mage::helper('dbm_share')->getPrettyType($value));

        return $prettyType;
    }
}