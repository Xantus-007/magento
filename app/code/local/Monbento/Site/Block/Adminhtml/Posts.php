<?php

class Monbento_Site_Block_Adminhtml_Posts extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_posts';
        $this->_blockGroup         = 'monbento_site';
        parent::__construct();
        
        $this->_headerText         = 'Ajouter un Slider / Bloc promotionnel';
        
        $this->_updateButton('add', 'label', Mage::helper('mageplaza_betterblog')->__('Add Post'));

        $this->setTemplate('mageplaza_betterblog/grid.phtml');
    }
    
    public function getCreateUrl()
    {
        return $this->getUrl('*/betterblog_post/new');
    }
}
