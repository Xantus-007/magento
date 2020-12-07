<?php

class Dbm_Share_Block_Adminhtml_Element_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $type = Mage::registry('dbm_share_current_type');
        $prettyType = Mage::helper('dbm_share')->getPrettyType($type);

        $this->_blockGroup = 'dbm_share';
        $this->_controller = 'adminhtml_element';
        $this->_headerText = 'Propriétés de la ' . $prettyType;


        $this->_updateButton('save', 'label', 'Sauvegarder la '.$prettyType);
        $this->_updateButton('delete', 'label', 'Supprimer la '.$prettyType);

        $this->_addButton('delete', array(
            'label'     => Mage::helper('catalog')->__('Delete'),
            'onclick'   => 'confirmSetLocation(\'Êtes-vous sûr de vouloir supprimer cette '.$prettyType.'?\', \''.$this->getUrl('*/*/delete', array('id'=>$this->getRequest()->getParam('id', 0))).'\')',
            'class'  => 'delete'
        ));

        parent::__construct();
    }
}