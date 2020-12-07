<?php 

class Mage_Adminhtml_Renderer_Enabledgift extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $lstOrderCert = Mage::getModel('ugiftcert/history')->getCollection()
            ->addFieldToFilter('cert_id', array('eq' => $row->getCertId()))
            ->addFieldToFilter('action_code', array('eq' => 'order'))
            ->addFieldToFilter('status', array('eq' => 'A'))
        ;
        
        return (count($lstOrderCert) > 0) ? $this->__('Oui') : $this->__('Non');
    }
}