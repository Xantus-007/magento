<?php

class Dbm_Share_Block_Adminhtml_Element_List_GridContainer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'dbm_share';
        $this->_controller = 'adminhtml_element_list';
        $this->_headerText = 'Gestion des partages';

        //$this->_updateButton('add', 'label', 'Ajouter une recette');
        unset($this->_buttons[0]['add']);
        $this->_addButton('add_receipe', array(
            'label' => 'Ajouter une recette',
            'class' => 'add',
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/newReceipe') .'\')'
        ));

        $this->_addButton('add_photo', array(
            'label' => 'Ajouter une photo',
            'class' => 'add',
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/newPhoto') .'\')'
        ));
    }
}