<?php

class Cartsguru_CartRecovery_Block_Pixel extends Mage_Core_Block_Template
{
    /**
     * Get the product added to cart that we saved in session
     */
    public function getAddToCartProduct()
    {
        $productData = Mage::getSingleton('core/session')->getCartsGuruAddToCart();
        if ($productData) {
            Mage::getSingleton('core/session')->unsCartsGuruAddToCart();
            return $productData;
        }
        return false;
    }

    /**
     * Get the tracking URL
     */
    public function getTrackingURL()
    {
        $params = array(
            '_secure' => Mage::app()->getRequest()->isSecure()
        );
        return $this->getUrl('cartsguru/saveaccount', $params);
    }
}
