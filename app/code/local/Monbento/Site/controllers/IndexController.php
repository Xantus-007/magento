<?php

class Monbento_Site_IndexController extends Mage_Core_Controller_Front_Action  
{

    public function customizeAction()
    {
        $productId  = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        // Render page
        try {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            // Register current data and dispatch final events
            Mage::register('current_product', $product);
            Mage::register('product', $product);

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');

            $this->addActionLayoutHandles();
            $this->loadLayoutUpdates();

            $this->generateLayoutXml()->generateLayoutBlocks();

            $this->renderLayout();
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }

    public function getRandomBundleAction()
    {
        if($productId = $this->getRequest()->getParam('id'))
        {
            $_product = Mage::getModel('catalog/product')->load($productId);
            $blockBundle = $this->getLayout()->createBlock('monbento_site/customize_configurables')->setData('product', $_product);

            $bundleOptionsRandom = $blockBundle->getBundleOptions(true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($bundleOptionsRandom));
            return;
        }

        $this->_forward('noRoute');
    }

    public function getResetBundleAction()
    {
        if($productId = $this->getRequest()->getParam('id'))
        {
            $_product = Mage::getModel('catalog/product')->load($productId);
            $blockBundle = $this->getLayout()->createBlock('monbento_site/customize_configurables')->setData('product', $_product);

            $bundleOptionsRandom = $blockBundle->getBundleOptions(false, true);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($bundleOptionsRandom));
            return;
        }

        $this->_forward('noRoute');
    }
    
    public function getProductImageAction()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $_product = Mage::getModel('catalog/product')->load($productId);
        echo Mage::helper('catalog/image')->init($_product, 'image')->resize(532);
    }

    public function getInstaBlockAction()
    {
        $response = array();

        try {
            $socialFeedsBlock = $this->getLayout()->createBlock('socialfeeds/items')
                ->setTemplate('socialfeeds/instagram/items.phtml')
                ->setData('offset', 0)
                ->setData('limit', 9)
                ->setData('size', 'medium')
                ->toHtml();

            $response['block'] = $socialFeedsBlock;
            $response['status'] = 'SUCCESS';
        } catch (Exception $e) {
            $response['status'] = 'ERROR';
            Mage::logException($e);
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
}