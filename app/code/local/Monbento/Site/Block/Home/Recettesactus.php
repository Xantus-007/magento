<?php

class Monbento_Site_Block_Home_Recettesactus extends Mage_Core_Block_Template 
{
    public function getBlocRecette()
    {
        $collectionRecette = Mage::getResourceModel('dbm_share/element_collection')
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('type', array('eq' => 'receipe'))
                    ->addFieldToFilter('show_in_home', array('eq' => 1))
                    ->setOrder('created_at', 'DESC');
        
        if($collectionRecette->getSize() == 0)
        {
            $collectionRecette = Mage::getModel('dbm_share/element')
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('type', array('eq' => 'receipe'))
                    ->setOrder('created_at', 'DESC');
        }
        
        return $collectionRecette->getFirstItem();
    }
    
    public function getBlocActu()
    {
        $collectionActu = Mage::getModel('blog/post')
                    ->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('status', array('eq' => 1))
                    ->addFieldToFilter('show_in_home', array('eq' => 1))
                    ->setOrder('created_time', 'DESC');
        
        if($collectionActu->getSize() == 0)
        {
            $collectionActu = Mage::getModel('blog/post')
                    ->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('status', array('eq' => 1))
                    ->setOrder('created_time', 'DESC');
        }

        return $collectionActu->getFirstItem();
    }
    
}