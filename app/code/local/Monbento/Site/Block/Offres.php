<?php

class Monbento_Site_Block_Offres extends Mage_Core_Block_Template 
{

    public function getOffres()
    {
        $pagesOffres = Mage::getModel('cms/page')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addFieldToFilter('is_active', array('eq' => 1))
                ->addFieldToFilter('root_template', array('eq' => 'recrutement'))
                ->addFieldToFilter('identifier', array('nin' => array('monbento-recrute', 'candidature-spontanee')))
                ->setOrder('page_id', 'desc');
        
        return $pagesOffres;
    }
    
}