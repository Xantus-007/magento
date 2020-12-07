<?php

class Dbm_TagManager_Block_Tag_Sliders extends Dbm_TagManager_Block_Tag
{

    protected function _construct()
    {
        $this->ecommerceData = array("ecommerce" => array("promoView" => array("promotions" => array())));
        
        $slides = ($this->hasSlides()) ? $this->getSlides() : $this->_getSlideCollection();
        if($slides)
        {
            $listName = ($_category = Mage::registry('current_category')) ? $_category->getName() : 'Home';
            
            $slidesData = array();
            $position = 1;
            foreach($slides as $slide)
            {
                $slidesData[] = array(
                    "id" => $slide->getEntityId(), 
                    "name" => $slide->getPostTitle(), 
                    "creative" => $slide->getImage(),
                    "list" => 'Promotions ' . $listName,
                    "position" => 'slider_slot'.$position
                );
                
                $position++;
            }
            
            if(!empty($slidesData)) 
            {
                $this->setLayerData($slidesData);
            }
        }
    }
    
    protected function _getSlideCollection()
    {
        /** @var Mage_Catalog_Block_Product_List $productListBlock */
        $slideBlock = Mage::app()->getLayout()->getBlock('promotions_slider');

        if (empty($slideBlock)) {
            //return null;
            $slideBlock = Mage::app()->getLayout()->getBlockSingleton('monbento_site/slides');
        }

        // Fetch the current collection from the block and set pagination
        $slides = $slideBlock->getSlides();

        return $slides;
    }
}