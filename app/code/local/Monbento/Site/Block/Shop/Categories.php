<?php

class Monbento_Site_Block_Shop_Categories extends Mage_Catalog_Block_Product_List 
{
    const ID_CAT_SHOP = 45;
    const ID_CAT_SOLDES = 117;
    
    public function getCatsCollection()
    {
        $cats = array();
        $now = Mage::getModel('core/date')->timestamp(time());
        $date = date('Y-m-d h:i:s', $now);
        
        $children = Mage::getModel('catalog/category')->getCollection()->setStoreId(Mage::app()->getStore()->getId());
        $children->addAttributeToSelect('*')
            ->addAttributeToFilter('parent_id', self::ID_CAT_SHOP)
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('include_in_page_shop', 1)
            ->addAttributeToFilter('entity_id', array('neq' => self::ID_CAT_SOLDES))    
            ->addAttributeToSort('position');
        
        foreach ($children as $child){
            $collectionProducts = Mage::getResourceModel('catalog/product_collection');
            $collectionProducts->getSelect()
                    ->joinLeft(array('cat' => 'catalog_category_product'), 'product_id = entity_id', array('category_id', 'cat_index_position' => 'position'))
                    ->where('cat.category_id IN (?)', array($child->getEntityId()));
            $collectionProducts
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('category_id', array('in' => array($child->getEntityId())))
                ->addAttributeToFilter('status', array('eq' => '1'))
                ->addAttributeToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE))
                ->addAttributeToFilter('select_for_shop', array('eq' => '1'));

            $cats[] = array('category' => $child, 'items' => $collectionProducts);
        }
        
        $outletCat = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load(self::ID_CAT_SOLDES);
        if($outletCat->getIsActive())
        {
            $collectionProductsOutlet = Mage::getResourceModel('catalog/product_collection');
            $collectionProductsOutlet->getSelect()
                        ->joinLeft(array('cat' => 'catalog_category_product'), 'product_id = entity_id', array('category_id', 'cat_index_position' => 'position'))
                        ->where('cat.category_id IN (?)', array($outletCat->getEntityId()));
            $collectionProductsOutlet
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', array('eq' => '1'))
                ->addAttributeToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE))
                ->addAttributeToFilter('select_for_shop', array('eq' => '1'))
                ->addAttributeToFilter('special_price',array('neq' => 0))
                ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $date))
                ->addAttributeToFilter(array(
                            array('attribute' => 'special_to_date', 'date' => true, 'from' => $date),
                            array('attribute' => 'special_to_date', 'is' => new Zend_Db_Expr('null'))
                ));

            $collectionProductsOutlet->getSelect()->order(new Zend_Db_Expr('RAND()'));
            $outletIds = $collectionProductsOutlet->getAllIds();
            $outletIds = array_slice($outletIds, 0, 3);

            $collectionProductsOutlet = Mage::getResourceModel('catalog/product_collection');
            $collectionProductsOutlet->getSelect()
                        ->joinLeft(array('cat' => 'catalog_category_product'), 'product_id = entity_id', array('category_id', 'cat_index_position' => 'position'))
                        ->where('cat.category_id IN (?)', array($outletCat->getEntityId()));
            $collectionProductsOutlet
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $outletIds));

            $cats[] = array('category' => $outletCat, 'items' => $collectionProductsOutlet);
        }
        
        //Mage::register('current_category', Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load(self::ID_CAT_SHOP));

        return $cats;
    }
    
}