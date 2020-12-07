<?php

class Monbento_Site_Model_Attribute_Source_Blocstemoignages extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
 
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            
            $blocsTemoignagesCatId = Mage::getStoreConfig('monbento_config/monbento_config_posts/monbento_blocs_temoignage_cat_id');

            $posts = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->addAttributeToSelect('*');
            if(!empty($blocsTemoignagesCatId)) $posts->addCategoryFilter($blocsTemoignagesCatId);
            $posts->setOrder('post_title', 'asc');
            
            $this->_options[] = array(
                'label' => 'Choisissez un bloc',
                'value' =>  ''
            );
            foreach($posts as $post)
            {
                $this->_options[] = array(
                    'label' => $post->getPostTitle(),
                    'value' =>  $post->getId()
                );
            }

        }
        return $this->_options;
    }
 
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
    
}