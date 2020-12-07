<?php

class Monbento_Site_Model_Observer
{

    public function salesQuoteCollectTotalsBefore(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getQuote();

        $freeShipping = Mage::helper('monbento_site')->quoteWithFreeShipping($quote);

        if ($freeShipping && $shippingAddress = $quote->getShippingAddress()) {
            $rates = $shippingAddress->collectShippingRates()
                    ->getGroupedAllShippingRates();

            foreach ($rates as $carrier) {
                foreach ($carrier as $rate) {
                    $carrierCode = $rate->getCode();
                    $rate->setPrice(0);
                }
            }
        }
    }

    public function disableOldGiftCert()
    {
        $certCollection = Mage::getModel('ugiftcert/cert')->getCollection()
                ->addFieldToFilter('status', array('eq' => 'A'));

        foreach ($certCollection as $cert) {
            $certExpirationDate = $cert->getExpireAt();
            if (date('Y-m-d') > $certExpirationDate) {
                $cert->setStatus('I')->save();
            }
        }

        return true;
    }

    public function salesSaveOrderExtraFieldsBefore(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $params = Mage::app()->getFrontController()->getRequest()->getParams();

        if (!empty($order)) {
            $typeSav = $params['order']['type_sav'];

            if($typeSav) $order->setTypeSav($typeSav);
        }

        return $this;
    }

    public function salesSaveShipmentExtraFieldsBefore(Varien_Event_Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $params = Mage::app()->getFrontController()->getRequest()->getParams();

        if (!empty($order)) {
            $fraisLivraisonReel = $params['order']['frais_livraison_reel'];

            $order->setFraisLivraisonReel($fraisLivraisonReel);
        }

        return $this;
    }

    public function updateShippingAddressFromPaypalReview(Varien_Event_Observer $observer)
    {
        $modeliv = $observer->getQuote()->getShippingAddress()->getShippingMethod(); // Récupération de la methode de livraison choisie par le client
	if(substr($modeliv,0,12) === 'dpdfrpredict' ||
           substr($modeliv,0,16) === 'owebiashipping1_' ||
           $modeliv === 'socolissimo_domicile_free_fr') {
            $shippingAddress = $observer->getQuote()->getShippingAddress(); // Recupération adresse de livraison
            $billingAddress = $observer->getQuote()->getBillingAddress(); // Récupération adresse de facturation

            $shippingAddress->setData('customer_id', $billingAddress->getData('customer_id'));
            $shippingAddress->setData('customer_address_id', $billingAddress->getData('customer_address_id'));
            $shippingAddress->setData('email', $billingAddress->getData('email'));
            $shippingAddress->setData('prefix', $billingAddress->getData('prefix'));
            $shippingAddress->setData('firstname', $billingAddress->getData('firstname'));
            $shippingAddress->setData('middlename', $billingAddress->getData('middlename'));
            $shippingAddress->setData('lastname', $billingAddress->getData('lastname'));
            $shippingAddress->setData('suffix', $billingAddress->getData('suffix'));
            $shippingAddress->setData('company', $billingAddress->getData('company'));
            $shippingAddress->setData('street', $billingAddress->getData('street'));
            $shippingAddress->setData('city', $billingAddress->getData('city'));
            $shippingAddress->setData('region', $billingAddress->getData('region'));
            $shippingAddress->setData('region_id', $billingAddress->getData('region_id'));
            $shippingAddress->setData('postcode', $billingAddress->getData('postcode'));
            $shippingAddress->setData('country_id', $billingAddress->getData('country_id'));
            $shippingAddress->save();
            $observer->getQuote()->setShippingAddress($shippingAddress);
            $observer->getQuote()->save();
        }

        Mage::dispatchEvent(
        'checkout_controller_onepage_save_shipping_method', array(
            'request' => $observer->getRequest(),
            'quote' => $observer->getQuote()));
    }

    public function updateBeforeAuthUrlAfterQuoteMerge(Varien_Event_Observer $observer)
    {
        $quote = $observer->getSource();
        if (0 < $quote->getItemsCount()) {
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(
                Mage::helper('checkout/cart')->getCartUrl()
            );
        }

        return $this;
    }
}
