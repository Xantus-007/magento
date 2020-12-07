<?php

class Dbm_Share_Block_Adminhtml_Comments_List_GridContainer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'dbm_share';
        $this->_controller = 'adminhtml_comments_list';
        $this->_headerText = 'Gestion des commentaires';
        
        $this->removeButton('add'); 

    }
}