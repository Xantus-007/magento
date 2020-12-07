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
 * @category    Quadra
 * @package     Quadra_Atos
 * @name        Quadra_Atos_Model_Observer
 * @author      Quadra Team
 */

class Quadra_Atos_Model_Observer
{
    /**
     *  Can redirect to Atos payment
     */
    public function initRedirect(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('checkout/session')->setCanRedirect(true);
    }    
    
    /**
     *  Return Orders Redirect URL
     *
     *  @return	  string Orders Redirect URL
     */
    public function multishippingRedirectUrl(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('checkout/session')->getCanRedirect()) {
            $orderIds = Mage::getSingleton('core/session')->getOrderIds();
            $orderIdsTmp = $orderIds;
            $key = array_pop($orderIdsTmp);
            $order = Mage::getModel('sales/order')->loadByIncrementId($key);

            if (!(strpos($order->getPayment()->getMethod(), 'atos') === false)) {
                Mage::getSingleton('checkout/session')
                        ->setLastOrderId($order->getId())
                        ->setRedirectUrl(Mage::getUrl('checkout/multishipping/success'))
                        ->setLastRealOrderId($order->getIncrementId())
                        ->setIsMultishipping(true)
                        ->setMultishippingOrderIds(implode(',', $orderIds));

                Mage::app()->getResponse()->setRedirect(Mage::getUrl('atos/standard/redirect'));
            }
        } else {
            Mage::getSingleton('checkout/session')
                    ->unsetData('is_multishipping')
                    ->unsetData('multishipping_order_ids');
        }
        
        return $this;
    }
    
    /**
     *  Disables sending email after the order creation
     *
     *  @return	  updated order
     */
    public function disableEmailForMultishipping(Varien_Event_Observer $observer)
    {
        $order = $observer->getOrder();
        
        if (!(strpos($order->getPayment()->getMethod(), 'atos') === false)) {
            $order->setCanSendNewEmailFlag(false)->save();
        }
        
        return $this;
    }
}
