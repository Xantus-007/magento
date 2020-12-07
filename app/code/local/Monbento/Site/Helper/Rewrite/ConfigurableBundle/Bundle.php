<?php

class Monbento_Site_Helper_Rewrite_ConfigurableBundle_Bundle extends Wizkunde_ConfigurableBundle_Helper_Bundle
{

    /**
     * We need to make sure the proper super attributes are selected in the buy request
     */
    public function formatConfigurableAttributesInBuyRequest($product)
    {
        $currentAttributes = $this->_getRequest()->getParam('super_attribute');

        foreach($this->_getRequest()->getParam('bundle_option') as $optionIterator => $bundleOption) {
            $realProduct = $this->resolveBundleSelectionId($product->getEntityId(), $bundleOption);
            if(isset($currentAttributes[$optionIterator])) {
                $currentAttributes[$realProduct] = $currentAttributes[$optionIterator];

                // Do not unset option 1 if it happens to be product 1
                if($optionIterator != $realProduct) {
                    unset($currentAttributes[$optionIterator]);
                }
            }
        }

        if($product->getModulePersonnalisation() == 1)
        {
            $simpleSelection = Mage::helper('monbento_bundle')->getSelectionsByBundleOptions($this->_getRequest()->getParams(), $product);

            $this->_getRequest()->setParam('super_attribute', $currentAttributes);
            $this->_getRequest()->setParam('simple_selection', $simpleSelection);
        }

        return $this->_getRequest()->getParams();
    }
}
