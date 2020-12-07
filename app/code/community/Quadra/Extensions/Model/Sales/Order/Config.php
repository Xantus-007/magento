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
 * @category   Quadra
 * @package    Quadra_Extensions
 * @name       Quadra_Extensions_Model_Sales_Order_Config
 * @author     Quadra Team
 */

class Quadra_Extensions_Model_Sales_Order_Config extends Mage_Sales_Model_Order_Config
{  
    /**
     * Retrieve statuses available for state
     * Get all possible statuses, or for specified state, or specified states array
     * Add labels by default. Return plain array of statuses, if no labels.
     *
     * @param mixed $state
     * @param bool $addLabels
     * @return array
     */
    public function getStateStatuses($state, $addLabels = true)
    {
        $version = Mage::getVersion();
        $version = substr($version, 0, 5);
        $version = str_replace('.', '', $version);
        while (strlen($version) < 3) {
            $version .= "0";
        }

        if (((int)$version) < 150) {
            return parent::getStateStatuses($state, $addLabels);
        }

        if (is_array($state)) {
            $key = implode('', $state) . $addLabels;
        } else {
            $key = $state . $addLabels;
        }        
        
        if (isset($this->_stateStatuses[$key])) {
            return $this->_stateStatuses[$key];
        }
        $statuses = array();
        if (empty($state) || !is_array($state)) {
            $state = array($state);
        }
        foreach ($state as $_state) {
            if ($stateNode = $this->_getState($_state)) {
                $collection = Mage::getResourceModel('sales/order_status_collection')
                    ->addStateFilter($_state)
                    ->orderByLabel();
                foreach ($collection as $status) {
                    $code = $status->getStatus();
                    if ($addLabels) {
                        $statuses[$code] = $status->getStoreLabel();
                    } else {
                        $statuses[] = $code;
                    }
                }
            }
        }
        $this->_stateStatuses[$key] = $statuses;
        return $statuses;
    }
}
