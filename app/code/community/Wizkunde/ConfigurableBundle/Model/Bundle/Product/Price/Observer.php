<?php

class Wizkunde_ConfigurableBundle_Model_Bundle_Product_Price_Observer extends Mage_CatalogRule_Model_Observer
{
    /**
     * Calculate price using catalog price rules of configurable product
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Mage_CatalogRule_Model_Observer
     */
    public function catalogProductTypeBundlePrice(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $price = $observer->getEvent()->getPrice();
        $selection = $observer->getEvent()->getSelection();

        $target = ($selection != null) ? $selection : $product;

        if ($product instanceof Mage_Catalog_Model_Product) {
            $target->setFinalPrice($price);

            $productPriceRule = Mage::getModel('catalogrule/rule')->calcProductPriceRule($product, $price);
            if ($productPriceRule !== null) {
                $target->setFinalPrice($productPriceRule);
            }
        }

        return $this;
    }
}