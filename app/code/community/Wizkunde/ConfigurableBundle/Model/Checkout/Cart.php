<?php

class Wizkunde_ConfigurableBundle_Model_Checkout_Cart extends Mage_Checkout_Model_Cart
{

    /**
     * @param int|Mage_Catalog_Model_Product $productInfo
     * @param null $requestInfo
     * @return Mage_Checkout_Model_Cart
     */
    public function addProduct($productInfo, $requestInfo=null)
    {
        $product = $this->_getProduct($productInfo);

        if($product->getTypeId() == 'bundle') {
            $newRequestInfo = Mage::helper('configurablebundle/bundle')->formatConfigurableAttributesInBuyRequest($product);
        }

        if(!isset($newRequestInfo)) {
            $newRequestInfo = $requestInfo;
        }
            
        return parent::addProduct($productInfo, $newRequestInfo);
    }

    /**
     * Get request for product add to cart procedure
     *
     * @param   mixed $requestInfo
     * @return  Varien_Object
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object(array('qty' => $requestInfo));
        } else {
             if(isset($requestInfo['bundle_has_custom_options'])){
                if(!empty($requestInfo['options'])){
                    foreach($requestInfo['bundle_simple_custom_options'] as $id => $opt){
                        foreach($opt['options'] as $key => $o){
                            $opt['options'][$key] = $requestInfo['options'][$key];
                            $requestInfo['bundle_simple_custom_options'][$id] = $opt;
                        }
                    }
                }

                unset($requestInfo['options']);
             }
            
            $request = new Varien_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }

        return $request;
    }
}
