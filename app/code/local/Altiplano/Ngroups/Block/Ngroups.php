<?php
class Altiplano_Ngroups_Block_Ngroups extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        $groups = Mage::getModel('ngroups/ngroups')->getCollection()->addFieldToFilter('visible', '1')->toArray();
        $this->groups = $groups;
        return parent::_prepareLayout();
    }
    
}