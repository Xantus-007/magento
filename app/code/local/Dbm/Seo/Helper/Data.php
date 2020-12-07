<?php

class Dbm_Seo_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Paths to module config options
     */
    const XML_PATH_SEO_LINK_ENABLED          = 'dbm_seo/defaultseo/seopagination_enabled';
    const XML_PATH_ALT_IMAGE    = 'dbm_seo/alt_tag/product_image';
    
    public function getAltProductImage($product) {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $altProductImage = Mage::getStoreConfig(self::XML_PATH_ALT_IMAGE);

            if(!empty($altProductImage)) {
                $categorie = '';
                $currentCategory = Mage::registry('current_category');
                if ($currentCategory && $currentCategory instanceof Mage_Catalog_Model_Category) {
                    $categorie = $currentCategory->getName();
                } else {
                    $categories = $product->getCategoryIds();
                    $allCatsMenu = Mage::getModel('catalog/category')->getCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('is_active','1')
                        ->addAttributeToFilter('include_in_menu','1')
                        ->addAttributeToSort('position', 'asc')
                        ->getAllIds();
                    $categories = array_intersect($categories, $allCatsMenu);
                    $categorie = Mage::getModel('catalog/category')->load(end($categories))->getName();
                }

                $alt = str_replace(array('[PRODUIT]', '[CATEGORIE]'), array($product->getName(), $categorie), $altProductImage);
                return $alt;
            } else {
                return $product->getName();
            }
        } else {
            return null;
        }
    }
    
    /**
     * Check whether the module and module output are enabled in system config
     *
     * @return bool
     */
    public function isLinkEnabled()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_SEO_LINK_ENABLED)) {
            return false;
        }
        if (!parent::isModuleOutputEnabled($this->_getModuleName())) {
            return false;
        }
        return true;
    }

}
