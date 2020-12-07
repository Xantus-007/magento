<?php

class Monbento_Site_Block_Presse extends Mage_Core_Block_Template 
{
    public function getBlocsPresse()
    {
        $blocs = array();
        
        $presseCatId = Mage::getStoreConfig('monbento_config/monbento_config_posts/monbento_blocs_presse_cat_id');

        $collectionPresse = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addCategoryFilter($presseCatId)
                ->addAttributeToFilter('status', array('eq' => 1))
                ->setOrder('custom_position', 'ASC');

        
        foreach($collectionPresse as $post)
        {
            $blocs[] = Mage::getModel('mageplaza_betterblog/post')->setStoreId(Mage::app()->getStore()->getId())->load($post->getEntityId());
        }

        return $blocs;
    }
    
}