<?php

class Wizkunde_ConfigurableBundle_Model_Bundle_Product_Price_Index extends Mage_Bundle_Model_Price_Index
{
    /**
     * Add bundle price range index to Product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Mage_Bundle_Model_Price_Index
     */
    public function addPriceIndexToCollection($collection)
    {
        $productObjects = array();
        $productIds     = array();
        foreach ($collection->getItems() as $product) {
            /* @var $product Mage_Catalog_Model_Product */
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                $productIds[] = $product->getEntityId();
                $productObjects[$product->getEntityId()] = $product;
            }
        }

        $websiteId  = Mage::app()->getStore($collection->getStoreId())
            ->getWebsiteId();
        $groupId    = Mage::getSingleton('customer/session')
            ->getCustomerGroupId();

        $addOptionsToResult = false;
        $prices = $this->_getResource()->loadPriceIndex($productIds, $websiteId, $groupId);
        foreach ($productIds as $productId) {
            if (isset($prices[$productId])) {
                $productObjects[$productId]
                    ->setData('_price_index', true)
                    ->setData('_price_index_min_price', $prices[$productId]['min_price'])
                    ->setData('_price_index_max_price', $prices[$productId]['max_price']);
            }
            else {
                $addOptionsToResult = true;
            }
        }

        if ($addOptionsToResult) {
            $collection->addOptionsToResult();
        }

        return $this;
    }

    /**
     * Add price index to bundle product after load
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Price_Index
     */
    public function addPriceIndexToProduct($product)
    {
        $websiteId  = $product->getStore()->getWebsiteId();
        $groupId    = Mage::getSingleton('customer/session')
            ->getCustomerGroupId();
        $prices = $this->_getResource()
            ->loadPriceIndex($product->getId(), $websiteId, $groupId);
        if (isset($prices[$product->getId()])) {
            $product->setData('_price_index', true)
                ->setData('_price_index_min_price', $prices[$product->getId()]['min_price'])
                ->setData('_price_index_max_price', $prices[$product->getId()]['max_price']);
        }

        return $this;
    }
}