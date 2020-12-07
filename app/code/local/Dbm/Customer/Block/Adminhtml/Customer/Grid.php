<?php


class Dbm_Customer_Block_Adminhtml_Customer_Grid extends Aitoc_Aitpermissions_Block_Rewrite_AdminCustomerGrid
{
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $collection = $this->getCollection();
        $collection->clear();
        $collection->addAttributeToSelect(array('profile_nickname', 'profile_status'));

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->removeColumn('nb_order');

        $this->addColumnAfter('profile_nickname', array(
            'header' => 'Pseudo club',
            'index' => 'profile_nickname',
        ), 'entity_id');
        $this->addColumnAfter('profile_status', array(
            'header' => 'Statut club',
            'index' => 'profile_status',
            'type' => 'options',
            'options' => $this->_getProfileStatus(),
        ), 'profile_nickname');
        $this->addColumnAfter('nb_order_by_dbm', array(
            'header'    => 'Nb commandes',
            'width'     => '90',
            'type'        => 'number',
            'align'     => 'center',
            'index'         => 'nb_order',
            'renderer'  => new Dbm_Share_Model_Adminhtml_Renderer_OrderCountByCustomer(),
        ), 'email');
        return parent::_prepareColumns();
    }

    protected function _getProfileStatus()
    {
        return Mage::helper('dbm_customer')->getProfileStatus('fr');
    }
}