<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Store model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @category   Mage
 * @package    Mage_Core
 */
class Monbento_Core_Model_Store extends Mage_Core_Model_Store
{

    /**
     * Retrieve current url for store
     *
     * @param bool|string $fromStore
     * @return string
     */
    public function getCurrentUrl($fromStore = true)
    {
        $query = Mage::getSingleton('core/url')->escape(ltrim(Mage::app()->getRequest()->getRequestString(), '/'));

        if (Mage::app()->getStore()->isCurrentlySecure()) {
            $parsedUrl = parse_url($this->getUrl('', array('_secure' => true)));
        } else {
            $parsedUrl = parse_url($this->getUrl(''));
        }
        $parsedQuery = array();
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parsedQuery);
        }

        foreach (Mage::app()->getRequest()->getQuery() as $k => $v) {
            $parsedQuery[$k] = $v;
        }

        if (!Mage::getStoreConfigFlag(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL, $this->getCode())) {
            //$parsedQuery['___store'] = $this->getCode();
        }
        if ($fromStore !== false) {
            $parsedQuery['___from_store'] = $fromStore === true ? Mage::app()->getStore()->getCode() : $fromStore;
        }

        return $parsedUrl['scheme'] . '://' . $parsedUrl['host']
            . (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '')
            . $parsedUrl['path'] . $query;
            //. ($parsedQuery ? '?'.http_build_query($parsedQuery, '', '&amp;') : '');
    }
}
