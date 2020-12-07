<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement Systempay
#						Version : 1.0b (révision 31978)
#									########################
#					Développé pour Magento
#						Version : 1.5.1.0
#						Compatibilité plateforme : V2
#									########################
#					Développé par Lyra Network
#						http://www.lyra-network.com/
#						16/12/2011
#						Contact : supportvad@lyra-network.com
#
#####################################################################################################

class Mage_Vads_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	
	protected $_code = 'vads';
	protected $_formBlockType = 'vads/form';
	protected $_infoBlockType = 'vads/info';

	// Is this payment method a gateway (online auth/charge) ?
	protected $_isGateway = true;

	// Can authorize online? => not implemented
	protected $_canAuthorize = true;

	// Can capture funds online? => not implemented
	protected $_canCapture = true;

	// Can capture partial amounts online?
	protected $_canCapturePartial = true;

	// Can refund online?
	protected $_canRefund = false;
	
	// Can refund partial online ?
	protected $_canRefundInvoicePartial     = false;

	// Can void transactions online? => yes but in Systempay backoffice
	protected $_canVoid = true;

	// Can use this payment method in administration panel?
	protected $_canUseInternal = true;

	// Can show this payment method as an option on checkout payment page?
	protected $_canUseCheckout = true;

	// Is this payment method suitable for multi-shipping checkout?
	protected $_canUseForMultishipping = true;

	// Can save credit card information for future processing?
	protected $_canSaveCc = false;

	/**
	 *  The url the customer is redirected to after clicking on "Confirm order"
	 *
	 *  @return	  string Order Redirect URL
	 */
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('vads/standard/payment');
	}
}
?>
