<?php

class Dbm_Expedition_Block_Adminhtml_Order_Grid extends Dbm_Share_Block_Adminhtml_Sales_Order_Grid
{

    protected function _prepareColumns()
    {

        $this->addColumnAfter('sender_admin_id', array(
            'header'    =>  Mage::helper('sales')->__('Expéditeur de commande'),
            'width'     =>  '100',
            'index'     =>  'sender_admin_id',
            'type'      =>  'text',
            'renderer'  =>  new Dbm_Expedition_Block_Adminhtml_Order_Renderer_Sender(),
            'filter_condition_callback' => array($this, 'senderAdminIdFilter')
        ), 'status');

        return parent::_prepareColumns();
    }
    
    protected function senderAdminIdFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $this->getCollection()->getSelect()
            ->joinLeft(array('admin_user' => 'admin_user'), 'admin_user.user_id = sof.sender_admin_id', array('firstname', 'lastname')) 
            ->where('sof.sender_admin_id <> 0')
            ->where("CONCAT(admin_user.firstname, ' ', admin_user.lastname) LIKE ?", "%$value%");

        return $this;
    }
}