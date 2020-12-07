<?php

class Dbm_Share_Block_Adminhtml_Comments_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'dbm_share';
        $this->_controller = 'adminhtml_comments';
        $this->_headerText = 'Propriétés du commentaire';


        parent::__construct();
    }
}