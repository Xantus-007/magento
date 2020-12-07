<?php

class Dbm_Seo_Model_Observer extends Mage_Core_Model_Abstract 
{

    /* Force canonical URL for product */
    public function forceProductCanonical(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('catalog/seo/product_canonical_tag') && !Mage::getStoreConfig('product_use_categories'))
        {
            if (Mage::getStoreConfig('dbm_seo/defaultseo/forcecanonical')) {
                // check for normal catalog/product/view controller here
                if(!stristr("catalog",Mage::app()->getRequest()->getModuleName()) && Mage::app()->getRequest()->getControllerName() != "product") return;
                $product = $observer->getEvent()->getProduct();
                $url = $product->getUrlModel()->getUrl($product, array('_ignore_category'=>true));
                
                /* PARAMETRE URL PAGE PRODUIT */
                if($_GET) {
                    $params = '?' . http_build_query($_GET, '', '|');
                    $url .= $params;
                }
                
                if(Mage::helper('core/url')->getCurrentUrl() != $url){
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url,301);
                    Mage::app()->getResponse()->sendResponse();
                }
            }
        }
    }
    
    /**
     * doPagination() - kick off the process of adding the next and prev 
     * rel links to category pages where necessary
     *
     * @return Dbm_Seo_Model_Observer
     */
    public function doPagination()
    {
        try {
            if (Mage::helper('dbm_seo')->isLinkEnabled()) {
                $paginator = Mage::getModel('dbm_seo/paginator');        
                $paginator->createLinks();
            }
        }
        catch(Exception $e) {
            Mage::logException($e);
        }
        
        return $this;
    }

    /* Set default meta description for product if product meta description is empty */
    public function setMetaProduct($observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product) {
            $descriptionProduct = $product->getMetaDescription();
            $descriptionGenerique = Mage::getStoreConfig('dbm_seo/meta_description/product');
            $titleProduct = $product->getMetaTitle();
            $titleGenerique = Mage::getStoreConfig('dbm_seo/meta_title/product');

            $categorie = '';
            $currentCategory = Mage::registry('current_category');
            if ($currentCategory && $currentCategory instanceof Mage_Catalog_Model_Category) {
                $categorie = $currentCategory->getName();
            }

            if(empty($descriptionProduct) && !empty($descriptionGenerique)) {
                $description = str_replace(array('[PRODUIT]', '[CATEGORIE]'), array($product->getName(), $categorie), $descriptionGenerique);
                $product->setMetaDescription($description);
            }

            if(empty($titleProduct) && !empty($titleGenerique)) {
                $title = str_replace(array('[PRODUIT]', '[CATEGORIE]'), array($product->getName(), $categorie), $titleGenerique);
                $product->setMetaTitle($title);
            }
        }
    }

    /* Set default meta description for category if category meta description is empty */
    public function setMetaCategory($observer)
    {
        $category = $observer->getEvent()->getCategory();

        if ($category) {
            $descriptionCategory = $category->getMetaDescription();
            $descriptionGenerique = Mage::getStoreConfig('dbm_seo/meta_description/category');
            $titleCategory = $category->getMetaTitle();
            $titleGenerique = Mage::getStoreConfig('dbm_seo/meta_title/category');

            if(empty($descriptionCategory) && !empty($descriptionGenerique)) {
                $description = str_replace(array('[CATEGORIE]', '[H1 CATEGORIE]'), array($category->getName(), $category->getMetaKeywords()), $descriptionGenerique);
                $category->setMetaDescription($description);
            }
            
            if(empty($titleCategory) && !empty($titleGenerique)) {
                $title = str_replace(array('[CATEGORIE]', '[H1 CATEGORIE]'), array($category->getName(), $category->getMetaKeywords()), $titleGenerique);
                $category->setMetaTitle($title);
            }
        }
    }

    /* Set default meta description for cms page */
    public function setMetaCms($observer)
    {
        $page = $observer->getEvent()->getPage();
        $identifier = $page->getIdentifier();

        if ($page && $identifier != 'home') {
            $descriptionGenerique = Mage::getStoreConfig('dbm_seo/meta_description/cms');
            $titleGenerique = Mage::getStoreConfig('dbm_seo/meta_title/cms');

            if(!empty($descriptionGenerique)) {
                $description = str_replace(array('[TITLE]'), array($page->getTitle()), $descriptionGenerique);
                $page->setMetaDescription($description);
            }
            
            if(!empty($titleGenerique)) {
                $title = str_replace(array('[TITLE]'), array($page->getTitle()), $titleGenerique);
                $page->setMetaTitle($title);
            }
        }
    }

    /**
     * Generate sitemaps
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledGenerateSitemaps($schedule)
    {
        $errors = array();

        $collection = Mage::getModel('sitemap/sitemap')->getCollection();
        /* @var $collection Mage_Sitemap_Model_Mysql4_Sitemap_Collection */
        foreach ($collection as $sitemap) {
            /* @var $sitemap Mage_Sitemap_Model_Sitemap */

            try {
                $sitemap->generateXml();
            }
            catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors && Mage::getStoreConfig(Mage_Sitemap_Model_Observer::XML_PATH_ERROR_RECIPIENT)) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('core/email_template');
            /* @var $emailTemplate Mage_Core_Model_Email_Template */
            $emailTemplate->setDesignConfig(array('area' => 'backend'))
                ->sendTransactional(
                    Mage::getStoreConfig(Mage_Sitemap_Model_Observer::XML_PATH_ERROR_TEMPLATE),
                    Mage::getStoreConfig(Mage_Sitemap_Model_Observer::XML_PATH_ERROR_IDENTITY),
                    Mage::getStoreConfig(Mage_Sitemap_Model_Observer::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings' => join("\n", $errors))
                );

            $translate->setTranslateInline(true);
        }
    }
    
}