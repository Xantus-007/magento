<?php

class Monbento_Site_Block_Slides extends Mage_Core_Block_Template
{

    public function getSlides()
    {
        $slides = array();

        if($category = Mage::registry('current_category'))
        {
            $ids = array();
            for($i=1;$i<4;$i++)
            {
                if($id = $category->getData('select_slider_'.$i)) $ids[] = $id;
            }

            $collectionSlides = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', array('in' => $ids))
                    ->addAttributeToFilter('status', array('eq' => 1))
                    ->setOrder('custom_position', 'ASC');
        }
        else
        {
            $slidesHomeCatId = Mage::getStoreConfig('monbento_config/monbento_config_posts/monbento_sliders_home_cat_id');

            $collectionSlides = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addAttributeToSelect('*')
                    ->addCategoryFilter($slidesHomeCatId)
                    ->addAttributeToFilter('status', array('eq' => 1))
                    ->setOrder('custom_position', 'ASC');
        }

        foreach($collectionSlides as $post)
        {
            $slides[] = Mage::getModel('mageplaza_betterblog/post')->setStoreId(Mage::app()->getStore()->getId())->load($post->getEntityId());
        }

        return $slides;
    }

    public function getTypoClassCSS($slide, $title)
    {
        switch ($slide->getData($title)) {
            case 1:
                //Sofia Pro Regular
                return 'slide-title-typo-spr';
            case 2:
                //Sofia Pro Bold
                return 'slide-title-typo-spb';
            case 3:
                //Sofia Pro Bold Condensed
                return 'slide-title-typo-spbc';
            case 4:
                //Sofia Pro Light
                return 'slide-title-typo-spl';
            case 5:
                //Sofia Pro ExtraLight
                return 'slide-title-typo-spel';
        }

        return '';
    }

    public function getSizeClassCSS($slide, $title)
    {
        switch ($slide->getData($title)) {
            case 1:
                //Sofia Pro Regular
                return 'slide-title-size-huge';
            case 2:
                //Sofia Pro Bold
                return 'slide-title-size-big';
            case 3:
                //Sofia Pro Bold Condensed
                return 'slide-title-size-normal';
            case 4:
                //Sofia Pro Light
                return 'slide-title-size-small';
        }

        return '';
    }

    public function getColorStyleCSS($slide, $title)
    {
        $hexaColor = $slide->getData($title);
        if (substr($hexaColor, 0, 1) != '#') {
            $hexaColor = '#' . $hexaColor;
        }
        if (preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $hexaColor)) {
            return 'color: ' . $hexaColor . ';';
        }

        return '';
    }
}