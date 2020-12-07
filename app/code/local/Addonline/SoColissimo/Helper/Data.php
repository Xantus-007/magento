<?php
/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Addonline_SoColissimo
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Module en mode Flexibilité ?
     * @return boolean
     */
    public function isFlexibilite()
    {
        return Mage::getStoreConfig('carriers/socolissimo/contrat') == 'flexibilite';
    }

    /**
     * Livraison à domicile avec signature activée
     */
    public function isDomicileAvecSignature()
    {
        return Mage::getStoreConfig('carriers/socolissimo/domicile_signature');
    }

    /**
     * Poids du panier
     * @return number
     */
    public function getQuoteWeight()
    {
        // on récupère le poids du colis en gramme de type entier
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $weight = 0;
        foreach ($quote->getAllItems() as $item) {
            $weight += $item->getRowWeight();
        }
        //$weight = round($weight * 1000);
        $weight = round($weight);
        if ($weight == 0) {
            $weight = 1;
        }
        return $weight;
    }

    /**
     * Date de livraison
     * @return string
     */
    public function getShippingDate()
    {
        $shippingDate = new Zend_Date();
        if ($delay = Mage::getStoreConfig('carriers/socolissimo/shipping_period')) {
            $shippingDate->addDay($delay);
        } else {
            $shippingDate->addDay(1);
        }
        return $shippingDate->toString('dd/MM/yyyy');
    }
}
