<?php

class Cartsguru_CartRecovery_Model_Cartsguru_Api_V2 extends Mage_Api_Model_Resource_Abstract
{

    public function admin($adminAction, $adminData=null)
    {
        /** @var \Cartsguru_CartRecovery_Helper_Data $helper */
        $helper = Mage::helper('cartsguru_cartrecovery');
        $result = null;
        // Toggle features action
        if ($adminAction === 'toggleFeatures' && $adminData !== null) {
            $data = json_decode($adminData, true);

            if (is_array($data)) {
                // Toggle facebook Display
                if (array_key_exists('facebook', $data)) {
                    if ($data['facebook'] == true) {
                        // Save facebook pixel
                        $helper->setStoreConfig('feature_facebook', true);

                        if (array_key_exists('catalogId', $data)) {
                            $helper->setStoreConfig('facebook_catalogId', $data['catalogId']);
                        }

                        if (array_key_exists('pixel', $data)) {
                            $helper->setStoreConfig('facebook_pixel', $data['pixel']);
                        }

                        if (array_key_exists('trackerUrl', $data)) {
                            $helper->setStoreConfig('tracker_url', $data['trackerUrl']);
                        }

                        // Clear cache
                        $helper->clearCache();
                        // return catalogUrl
                        $result = array(
                            'catalogUrl' => Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . 'cartsguru/catalog',
                            'CARTSG_FEATURE_FB' => true
                        );
                    } elseif ($data['facebook'] == false) {
                        $helper->setStoreConfig('feature_facebook', false);
                        // Clear cache
                        $helper->clearCache();
                        $result = array('CARTSG_FEATURE_FB' => false);
                    }
                }
                // Toggle facebook messenger
                if (array_key_exists('fbm', $data)) {
                    if ($data['fbm'] == true && array_key_exists('appId', $data) && array_key_exists('pageId', $data)) {
                        $helper->setStoreConfig('feature_fbm', true);
                        $helper->setStoreConfig('facebook_pageId', $data['pageId']);
                        $helper->setStoreConfig('facebook_appId', $data['appId']);

                        if (array_key_exists('trackerUrl', $data)) {
                            $helper->setStoreConfig('tracker_url', $data['trackerUrl']);
                        }

                        // Clear cache
                        $helper->clearCache();
                        $result = array('feature_fbm' => true);
                    } elseif ($data['fbm'] == false) {
                        $helper->setStoreConfig('feature_fbm', false);
                        // Clear cache
                        $helper->clearCache();
                        $result = array('feature_fbm' => false);
                    }
                }
                // Toggle CI
                if (array_key_exists('ci', $data)) {
                    if ($data['ci'] == true) {
                        $helper->setStoreConfig('feature_ci', true);

                        if (array_key_exists('trackerUrl', $data)) {
                            $helper->setStoreConfig('tracker_url', $data['trackerUrl']);
                        }

                        $helper->clearCache();
                        $result = array('feature_ci' => true);
                    } elseif ($data['ci'] == false) {
                        $helper->setStoreConfig('feature_ci', false);
                        $helper->clearCache();
                        $result = array('feature_ci' => false);
                    }
                }
                // Toogle widgets
                if (array_key_exists('widgets', $data) && is_array($data['widgets'])) {
                    $helper->setStoreConfig('feature_widgets', json_encode($data['widgets']));
                    $helper->clearCache();
                    $result = array('CARTSG_WIDGETS' => $data['widgets']);
                }
            }
        }
        // Get config
        if ($adminAction === 'displayConfig') {
            $result = array(
                'CARTSG_API_SUCCESS' => $helper->getStoreConfig('apiSuccess'),
                'CARTSG_SITE_ID' => $helper->getStoreConfig('siteid'),
                'CARTSG_FEATURE_FB' => $helper->getStoreConfig('feature_facebook'),
                'CARTSG_FB_PIXEL' => $helper->getStoreConfig('facebook_pixel'),
                'CARTSG_FB_CATALOGID' => $helper->getStoreConfig('facebook_catalogId'),
                'CARTSG_FEATURE_FBM' => $helper->getStoreConfig('feature_fbm'),
                'CARTSG_FB_PAGEID' => $helper->getStoreConfig('facebook_pageId'),
                'CARTSG_FB_APPID' => $helper->getStoreConfig('facebook_appId'),
                'CARTSG_TRACKERURL' => $helper->getStoreConfig('tracker_url'),
                'CARTSG_FEATURE_CI' => $helper->getStoreConfig('feature_ci'),
                'CARTSG_WIDGETS' => $helper->getStoreConfig('feature_widgets'),
                'PLUGIN_VERSION' => (string)Mage::getConfig()->getNode()->modules->Cartsguru->version
            );
        }
        // Get store coupons
        if ($adminAction === 'getCoupons') {
            $result = $helper->getCartRules();
        }
        // Create coupons in store
        if ($adminAction === 'createCoupons') {
            $data = json_decode($adminData, true);
            $result = $helper->createCartRules($data);
        }
        // Delete coupons from store
        if ($adminAction === 'deleteCoupons') {
            $data = json_decode($adminData, true);
            $result = $helper->deleteCartRules($data);
        }
        // Send result if present
        if ($result) {
             $result = Mage::helper('core')->jsonEncode($result);
             return ['result'=> $result];
        }
    }

}
