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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Dbm_Country_Model_System_Config_Source_Countryallowed
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
        
        $countryAllowed = explode(',', Mage::getStoreConfig('general/country/allow'));
        
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
        }

        $options = $this->_options;
        
        foreach ($options as $key => $option) {
            if(!in_array($option['value'], $countryAllowed)){
                unset($options[$key]);
            }
        }
        
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
}
