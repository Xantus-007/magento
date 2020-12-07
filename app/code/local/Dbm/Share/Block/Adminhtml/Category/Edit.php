<?php

class Dbm_Share_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'dbm_share';
        $this->_controller = 'adminhtml_category';
        $this->_headerText = 'Catégorie de partage';

        $this->_updateButton('save', 'label', 'Sauvegarder');
        $this->_updateButton('delete', 'label', 'Supprimer la catégorie');

        $this->_addButton('delete', array(
            'label'     => Mage::helper('catalog')->__('Supprimer'),
            'onclick'   => 'confirmSetLocation(\'Êtes-vous sûr de vouloir supprimer cette catégorie?\', \''.$this->getUrl('*/*/delete', array('id'=>$this->getRequest()->getParam('id', 0))).'\')',
            'class'  => 'delete'
        ));

        parent::__construct();
    }
}