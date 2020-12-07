<?php

class Dbm_Share_Block_Menu_Main extends Dbm_Share_Block_Menu_Abstract
{
    public function getCurrentMenu()
    {
        return Dbm_Share_Helper_Data::REG_MENU_MAIN;
    }
    
    public function getLinks()
    {
        $trans = Mage::helper('dbm_share');
        
        return array(
            $this->getUrl('club/index/index/') => array('label' => $trans->__('News feed')),
            $this->getUrl('club/index/subscriptions') => array('label' => $trans->__('My subscriptions')),
            $this->getUrl('club/index/subscribers') => array('label' => $trans->__('My followers')),
            $this->getUrl('club/index/liked') => array('label' => $trans->__('My likes')),
            $this->getUrl('club/index/recipes') => array('label' => $trans->__('My recipes')),
            $this->getUrl('club/index/photos') => array('label' => $trans->__('My photos')),
            $this->getUrl('club/index/pepites/club/1') => array('label' => $trans->__('My pepites'))
        );
    }
}