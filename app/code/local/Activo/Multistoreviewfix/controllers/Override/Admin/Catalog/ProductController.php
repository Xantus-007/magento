<?php
/**
 * Activo fix for bug# 25062
 * http://www.magentocommerce.com/bug-tracking/issue?issue=10705
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @package     Activo_Multistoreviewfix
 * @copyright   Copyright (c) 2011 Activo Inc. (http://www.activo.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * 
 * Modification based on code written by @millejano (http://www.magentocommerce.com/boards/member/2071/)
 * Controller rewrite functionality based on Lee Saferite (http://www.magentocommerce.com/boards/member/139/)
 * 
 * Extension provided by Activo (www.activo.com)
 * Author: Ron Peled (http://twitter.com/ronpeled)
 */

include("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Activo_Multistoreviewfix_Override_Admin_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    /**
     * Initialize product before saving
     */
    protected function _initProductSave()
    {
        $product    = $this->_initProduct();
        $productData = $this->getRequest()->getPost('product');
        if ($productData && !isset($productData['stock_data']['use_config_manage_stock'])) {
            $productData['stock_data']['use_config_manage_stock'] = 0;
        }

        /**
         * Websites
         */
        if (!isset($productData['website_ids'])) {
            $productData['website_ids'] = array();
        }

        $wasLockedMedia = false;
        if ($product->isLockedAttribute('media')) {
            $product->unlockAttribute('media');
            $wasLockedMedia = true;
        }

        $product->addData($productData);

        if ($wasLockedMedia) {
            $product->lockAttribute('media');
        }

        if (Mage::app()->isSingleStoreMode()) {
            $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        }

        /**
         * Create Permanent Redirect for old URL key
         */
        if ($product->getId() && isset($productData['url_key_create_redirect']))
        // && $product->getOrigData('url_key') != $product->getData('url_key')
        {
            $product->setData('save_rewrites_history', (bool)$productData['url_key_create_redirect']);
        }

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) 
            {
                // + this workaround is only needed for configurable products Issue #25227 [code@bytepark.de]
                if(true === $product->isConfigurable()) {
                    // if there is no entry for the attribute code in $_REQUEST['product'] 
                    if(!array_key_exists($attributeCode, $productData)) {
                        $product->setData($attributeCode, false);
                    }
                }
                else {
                    // do the normal in the normal way
                    $product->setData($attributeCode, false);
                }
                // = this workaround is only needed for configurable products Issue #25227 [code@bytepark.de]
            }
            
//            foreach ($useDefaults as $attributeCode) {
//                $product->setData($attributeCode, false);
//            }

        }

        /**
         * Init product links data (related, upsell, crosssel)
         */
        $links = $this->getRequest()->getPost('links');
        if (isset($links['related']) && !$product->getRelatedReadonly()) {
            $product->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));
        }
        if (isset($links['upsell']) && !$product->getUpsellReadonly()) {
            $product->setUpSellLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['upsell']));
        }
        if (isset($links['crosssell']) && !$product->getCrosssellReadonly()) {
            $product->setCrossSellLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['crosssell']));
        }
        if (isset($links['grouped']) && !$product->getGroupedReadonly()) {
            $product->setGroupedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['grouped']));
        }

        /**
         * Initialize product categories
         */
        $categoryIds = $this->getRequest()->getPost('category_ids');
        if (null !== $categoryIds) {
            if (empty($categoryIds)) {
                $categoryIds = array();
            }
            $product->setCategoryIds($categoryIds);
        }

        /**
         * Initialize data for configurable product
         */
        if (($data = $this->getRequest()->getPost('configurable_products_data')) && !$product->getConfigurableReadonly()) {
            $product->setConfigurableProductsData(Mage::helper('core')->jsonDecode($data));
        }
        if (($data = $this->getRequest()->getPost('configurable_attributes_data')) && !$product->getConfigurableReadonly()) {
            $product->setConfigurableAttributesData(Mage::helper('core')->jsonDecode($data));
        }

        $product->setCanSaveConfigurableAttributes((bool)$this->getRequest()->getPost('affect_configurable_product_attributes') && !$product->getConfigurableReadonly());

        /**
         * Initialize product options
         */
        if (isset($productData['options']) && !$product->getOptionsReadonly()) {
            $product->setProductOptions($productData['options']);
        }

        $product->setCanSaveCustomOptions((bool)$this->getRequest()->getPost('affect_product_custom_options') && !$product->getOptionsReadonly());

        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));

        return $product;
    }


}
