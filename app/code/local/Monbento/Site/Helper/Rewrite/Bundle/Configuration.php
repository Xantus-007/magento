<?php

class Monbento_Site_Helper_Rewrite_Bundle_Configuration extends Wizkunde_ConfigurableBundle_Helper_Catalog_Product_Configuration
{
    /**
     * Get all the configurable attributes that have been set in the order
     *
     * @param Mage_Sales_Model_Order_Item $item
     */
    public function getConfigurableAttributes(Mage_Sales_Model_Order_Item $item) 
    {
        $buyRequest = $item->getBuyRequest();
        $itemOptions = $item->getData('product_options');
        $itemOptions = unserialize($itemOptions);

        if($buyRequest != null && isset($buyRequest['super_attribute']) && count($buyRequest['super_attribute'] > 0)) {
            $selectionHtml = '<div style="clear: both;margin-left: 20px;">';

            $superAttributes = (isset($buyRequest['super_attribute'][$item->getProductId()])) ?
                $buyRequest['super_attribute'][$item->getProductId()] :
                $buyRequest['super_attribute'];

            if(isset($itemOptions['bundle_selection_attributes']))
            {
                $configurableSelection = unserialize($itemOptions['bundle_selection_attributes']);
                $optionId = $configurableSelection['option_id'];
                $productId = $buyRequest['simple_selection']['bundle-option-'.$optionId];
                $simpleProduct = Mage::getModel('catalog/product')->load($productId);

                $selectionHtml .= $simpleProduct->getAttributeText('matiere') . ': ' . $simpleProduct->getAttributeText('color') . '<br />';
            }
            else
            {
                foreach($superAttributes as $attributeId => $attributeValue) {
                    $attr = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
                    $selectionHtml .= $attr->getFrontendLabel() . ': ' . $attr->getSource()->getOptionText($attributeValue) . '<br />';
                }
            }

            $selectionHtml .= '</div>';
        }

        return $selectionHtml;
    }
    
    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @return array
     */
    public function getBundleOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $options = array();
        $product = $item->getProduct();

        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $product->getTypeInstance(true);

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption ? unserialize($optionsQuoteItemOption->getValue()) : array();
        if ($bundleOptionsIds) {
            /**
            * @var Mage_Bundle_Model_Mysql4_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $bundleSelectionIds = unserialize($selectionsQuoteItemOption->getValue());

            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds(
                    unserialize($selectionsQuoteItemOption->getValue()),
                    $product
                );

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = array(
                            'label' => $bundleOption->getTitle(),
                            'value' => array()
                        );

                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                            if ($qty) {
                                if($this->getSelectionFinalPrice($item, $bundleSelection) == 0)
                                {
                                    $option['value'][] = $qty . ' x ' . $this->escapeHtml($bundleSelection->getName());
                                }
                                else
                                {
                                    $option['value'][] = $qty . ' x ' . $this->escapeHtml($bundleSelection->getName())
                                    . ' ' . Mage::helper('core')->currency(
                                        $this->getSelectionFinalPrice($item, $bundleSelection)
                                    );
                                }
                            }
                        }

                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }

}
