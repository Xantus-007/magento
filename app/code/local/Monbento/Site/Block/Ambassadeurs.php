<?php

class Monbento_Site_Block_Ambassadeurs extends Mage_Core_Block_Template 
{
    public function getBlocsAmbassadeur()
    {
        $blocs = array();
        
        $ambassadeurCatId = Mage::getStoreConfig('monbento_config/monbento_config_posts/monbento_blocs_ambassadeur_cat_id');

        $collectionAmbassadeur = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addCategoryFilter($ambassadeurCatId)
                ->addAttributeToFilter('status', array('eq' => 1))
                ->setOrder('custom_position', 'ASC');

        
        foreach($collectionAmbassadeur as $post)
        {
            $blocs[] = Mage::getModel('mageplaza_betterblog/post')->setStoreId(Mage::app()->getStore()->getId())->load($post->getEntityId());
        }

        return $blocs;
    }
    
}