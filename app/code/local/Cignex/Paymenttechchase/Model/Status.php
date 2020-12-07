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
  * @category    Cignex
  * @package     Cignex_Paymentchase
  * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Cignex_Paymenttechchase_Model_Status extends Varien_Object
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('paymenttechchase')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('paymenttechchase')->__('Disabled')
        );
    }
}