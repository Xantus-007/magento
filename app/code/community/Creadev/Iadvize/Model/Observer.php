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
 * @category   Creadev
 * @package    Creadev_iAdvize
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * iAdvize module observer
 *
 * @category   Creadev
 * @package    Creadev_iAdvize
 */
class Creadev_iAdvize_Model_Observer
{

		public function prepareForm(Varien_Event_Observer $observer){
		    $form = $observer->getEvent()->getForm();

    		$fieldset = $form->addFieldset('iadvize_fieldset', array('legend'=>Mage::helper('cms')->__('Iadvize')));

				$fieldset->addField('iadvize', 'select', array(
		        'name'      => 'iadvize',
    		    'label'     => Mage::helper('cms')->__('Iadvize'),
		        'title'     => Mage::helper('cms')->__('Iadvize'),
		        'options'   => array(
                '1' => 'Oui',
                '0' => 'Non',
            ),
        		'disabled'  => $isElementDisabled,
    		));

		}

		public function savePage(Varien_Event_Observer $observer) {

		}

}