<?php

class Dbm_Share_Block_Menu_Top extends Dbm_Share_Block_Menu_Abstract
{
    public function getCurrentMenu()
    {
        return Dbm_Share_Helper_Data::REG_MENU_TOP;
    }
    
    public function getLinks()
    {
        $trans = Mage::helper('dbm_share');
        $helper = Mage::helper('dbm_blog');
        
        return array(
            $this->getUrl('club/index/index/') => array(
                'label' => $trans->__('Profile'),
                'image' => $this->getSkinUrl('images/svg/clubento/profil.svg'),
                'class' => 'club-profile'
            ),
            $this->getUrl('club/photos/index') => array(
                'label' => $trans->__('Photos'),
                'image' => $this->getSkinUrl('images/svg/clubento/photos.svg')
            ),
            $this->getUrl('club/recipes/index/') => array(
                'label' => $trans->__('Recipes'),
                'image' => $this->getSkinUrl('images/svg/clubento/receipes.svg')
            ),
            $this->getUrl('club/videos/index') => array(
                'label' => $trans->__('Videos'),
                'image' => $this->getSkinUrl('images/svg/clubento/videos.svg')
            ),
            $this->getUrl($helper->getBlogUrlForCurrentStore()) => array(
                'label' => $trans->__('News'),
                'image' => $this->getSkinUrl('images/svg/clubento/news.svg'),
                'class' => 'club-blog'
            )
        );
    }
}