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
 * @package    Quadra_Atos
 * @name        Quadra_Atos_Block_Euro_Redirect
 * @author      Quadra Team
 */

class Quadra_Atos_Block_Euro_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $euro = Mage::getModel('atos/method_euro');
        $euro->callRequest();

        /* $html = '';
          $html.= $euro->getSystemMessage(); */

        /* $payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment();
          if (!$card = $payment->getData('cc_type')) {
          $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
          $payment = $order->getPayment();
          $card = $payment->getData('cc_type');
          } */

        if ($euro->getSystemError()) {
            return $euro->getSystemMessage();
        }

        $html = '';

        $html.= '<style type="text/css">' . "\n";
        $html.= '  @import url("' . $this->getSkinUrl('css/stylesheet.css') . '");' . "\n";
        $html.= '  @import url("' . $this->getSkinUrl('css/checkout.css') . '");' . "\n";
        $html.= '</style>' . "\n";

        $html.= '<div id="atosButtons" style="display: none;">' . "\n";
        $html.= '  <p class="center">' . $this->__('You have to pay to validate your order') . '</p>' . "\n";
        $html.= '  <form id="atos_payment_checkout" action="' . $euro->getSystemUrl() . '" method="post">' . "\n";
        $html .= '<input type="hidden" name="1EUROCOM_x" value="1" />';
        $html .= '<input type="hidden" name="1EUROCOM_y" value="1" />';
        $html.= $euro->getSystemMessage() . "\n";
        $html.= '  </form>' . "\n";
        $html.= '</div>' . "\n";
        $html.= '<script type="text/javascript">document.getElementById("atos_payment_checkout").submit();</script>';

        return $html;
    }
}