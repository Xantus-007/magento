<?php

class Dbm_Share_Block_Adminhtml_Category_List_GridContainer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'dbm_share';
        $this->_controller = 'adminhtml_category_list';
        $this->_headerText = 'Gestion des catégories';

        $this->_updateButton('add', 'label', 'Ajouter une catégorie');
    }
}