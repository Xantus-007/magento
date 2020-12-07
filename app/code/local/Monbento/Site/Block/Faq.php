<?php

class Monbento_Site_Block_Faq extends Mage_Core_Block_Template 
{
    public function getFirtLevelCat()
    {
        $cats = array();

        $catsFaq = Mage::getResourceModel('mageplaza_betterblog/category_collection')
                ->addStoreFilter(Mage::app()->getStore())
                ->addPathFilter('1/' . $this->_getFaqCatId() . '/')
                ->addLevelFilter(2)
                ->addFieldToFilter('status', 1)
                ->setOrder('position', 'ASC');

        foreach($catsFaq as $cat)
        {
            $catId = $cat->getId();
            $cats[$catId] = $cat->getName();
        }

        return $cats;
    }

    public function getSecondLevelCat($parentCat)
    {
        $cats = array();

        $catsFaq = Mage::getResourceModel('mageplaza_betterblog/category_collection')
                ->addStoreFilter(Mage::app()->getStore())
                ->addPathFilter('1/' . $this->_getFaqCatId() . '/' . $parentCat . '/')
                ->addFieldToFilter('status', 1)
                ->setOrder('position', 'ASC');

        foreach($catsFaq as $cat)
        {
            $catId = $cat->getId();
            $cats[$catId] = $cat->getName();
        }

        return $cats;
    }

    public function getRecurringQuestion()
    {
        $recurring = array();

        $collectionRecurring = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addCategoryFilter($this->_getFaqCatId())
                ->addAttributeToFilter('status', array('eq' => 1))
                ->addAttributeToFilter('question_recurrente', array('eq' => 1));

        foreach($collectionRecurring as $ask)
        {
            $askId = $ask->getId();
            $recurring[$askId] = $ask->getPostTitle();
        }

        return $recurring;  
    }

    public function getQuestionByCat($parentCat)
    {
        $asks = array();
        
        $collectionAsks = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addCategoryFilter($parentCat)
                ->addAttributeToFilter('status', array('eq' => 1));

        
        foreach($collectionAsks as $ask)
        {
            $asks[] = Mage::getModel('mageplaza_betterblog/post')->setStoreId(Mage::app()->getStore()->getId())->load($ask->getEntityId());
        }

        return $asks;
    }

    protected function _getFaqCatId()
    {
        return Mage::getStoreConfig('monbento_config/monbento_config_posts/monbento_blocs_faq_cat_id');
    }
    
}