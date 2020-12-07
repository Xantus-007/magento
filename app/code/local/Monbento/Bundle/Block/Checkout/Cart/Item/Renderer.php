<?php
class Monbento_Bundle_Block_Checkout_Cart_Item_Renderer extends Mage_Bundle_Block_Checkout_Cart_Item_Renderer
{
    protected function _getBundleOptions($useCache = true)
    {
        $options = array();

        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $this->getProduct()->getTypeInstance(true);

        // get bundle options
        $optionsQuoteItemOption =  $this->getItem()->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = unserialize($optionsQuoteItemOption->getValue());
        if ($bundleOptionsIds) {
            /**
            * @var Mage_Bundle_Model_Mysql4_Option_Collection
            */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $this->getProduct());

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $this->getItem()->getOptionByCode('bundle_selection_ids');

            $selectionsCollection = $typeInstance->getSelectionsByIds(
                unserialize($selectionsQuoteItemOption->getValue()),
                $this->getProduct()
            );

            $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            foreach ($bundleOptions as $bundleOption) {
                if ($bundleOption->getSelections()) {
					$optionTitle = explode('-',$bundleOption->getTitle());
                    $option = array('label' => $optionTitle[1],'selectionId' => '','optionId' => $bundleOption->getId(),'productId' => '','type' => $optionTitle[0], "value" => array());
                    $bundleSelections = $bundleOption->getSelections();

                    foreach ($bundleSelections as $bundleSelection) {
                        $option['selectionId'] = $bundleSelection->getSelectionId();
                        $option['productId'] = $bundleSelection->getEntityId();
                        $option['value'][] = $this->_getSelectionQty($bundleSelection->getSelectionId()).' x '. $this->htmlEscape($bundleSelection->getName());
                    }

                    $options[] = $option;
                }
            }
			//$options[] = array("label" =>$this->__('Color of the food cup'),"value"=>array($this->__('1 x food cup for MB Original in color of top container')));
        }
        return $options;
    }
}
