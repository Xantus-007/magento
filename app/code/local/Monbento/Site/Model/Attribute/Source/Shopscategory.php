<?php

class Monbento_Site_Model_Attribute_Source_Shopscategory extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const ID_CAT_SHOP = 45;

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array();

            $cats = Mage::getModel('catalog/category')->getCollection()->setStoreId(Mage::app()->getStore()->getId());
            $cats->addAttributeToSelect('*')
                ->addAttributeToFilter('parent_id', self::ID_CAT_SHOP)
                ->addAttributeToFilter('is_active', 1)
                ->addAttributeToFilter('include_in_page_shop', 1)
                ->addAttributeToSort('position');
                        
            $this->_options[] = array(
                'label' => 'Choisissez une catégorie',
                'value' =>  ''
            );
            foreach($cats as $cat)
            {
                $this->_options[] = array(
                    'label' => $cat->getName(),
                    'value' =>  $cat->getEntityId()
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