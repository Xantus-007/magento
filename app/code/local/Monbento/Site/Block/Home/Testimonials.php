<?php

class Monbento_Site_Block_Home_Testimonials extends Mage_Core_Block_Template 
{
    public function getTestimonials()
    {
        $blocs = array();
        
        $ids = array();
        for($i=1;$i<4;$i++)
        {
            if($id = Mage::getStoreConfig('monbento_admin_wysiwyg/monbento_config_blocs_temoignages/monbento_temoignages_home_'.$i)) $ids[] = $id;
        }

        $collectionBlocs = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', array('in' => $ids))
                    ->addAttributeToFilter('status', array('eq' => 1));
        
        foreach($collectionBlocs as $post)
        {
            $blocs[] = Mage::getModel('mageplaza_betterblog/post')->setStoreId(Mage::app()->getStore()->getId())->load($post->getEntityId());
        }

        return $blocs;
    }
    
}