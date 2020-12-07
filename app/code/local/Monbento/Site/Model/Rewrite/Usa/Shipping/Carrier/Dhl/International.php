<?php

class Monbento_Site_Model_Rewrite_Usa_Shipping_Carrier_Dhl_International extends Mage_Usa_Model_Shipping_Carrier_Dhl_International
{
    
    protected $customRateInfo = [
        // Afrique du sud
        'ZA' => [
            'amount' => 28.00,
            'currencyCode' => 'EUR'
        ],
        // Allemagne
        'DE' => [
            'amount' => 7.48,
            'currencyCode' => 'EUR'
        ],
        // Argentine
        'AR' => [
            'amount' => 32.00,
            'currencyCode' => 'EUR'
        ],
        // Autriche
        'AU' => [
            'amount' => 8.25,
            'currencyCode' => 'EUR'
        ],
        // Belgique
        'BE' => [
            'amount' => 8.25,
            'currencyCode' => 'EUR'
        ],
        // Bulgarie
        'BG' => [
            'amount' => 11.92,
            'currencyCode' => 'EUR'
        ],
        // Canada
        'CA' => [
            'amount' => 9.90,
            'currencyCode' => 'USD'
        ],
        // Chili
        'CL' => [
            'amount' => 32.00,
            'currencyCode' => 'EUR'
        ],
        // Chine
        'CN' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Corée du sud
        'KR' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Croatie
        'HR' => [
            'amount' => 11.92,
            'currencyCode' => 'EUR'
        ],
        // Egypte
        'EG' => [
            'amount' => 28.00,
            'currencyCode' => 'EUR'
        ],
        // Emirats Arabes Unis
        'AE' => [
            'amount' => 28.00,
            'currencyCode' => 'EUR'
        ],
        // Espagne
        'ES' => [
            'amount' => 8.25,
            'currencyCode' => 'EUR',
            'postCodeNotAllowed' => [
                'from' => 35,
                'to' => 38
            ]
        ],
        // Estonie
        'EE' => [
            'amount' => 12.50,
            'currencyCode' => 'EUR'
        ],
        // Etats Unis
        'US' => [
            'amount' => 9.90,
            'currencyCode' => 'USD'
        ],
        // Finlande
        'FI' => [
            'amount' => 9.08,
            'currencyCode' => 'EUR'
        ],
        // Grèce
        'GR' => [
            'amount' => 9.08,
            'currencyCode' => 'EUR'
        ],
        // Guadeloupe
        'GP' => [
            'amount' => 23.33,
            'currencyCode' => 'EUR'
        ],
        // Guyane
        'GY' => [
            'amount' => 23.33,
            'currencyCode' => 'EUR'
        ],
        // Hong Kong
        'HK' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Hongrie
        'HU' => [
            'amount' => 11.92,
            'currencyCode' => 'EUR'
        ],
        // Inde
        'IN' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Indonésie
        'ID' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Irlande
        'IE' => [
            'amount' => 8.25,
            'currencyCode' => 'EUR'
        ],
        // Israël
        'IL' => [
            'amount' => 25.00,
            'currencyCode' => 'EUR'
        ],
        // Italie
        'IT' => [
            'amount' => 8.11,
            'currencyCode' => 'EUR'
        ],
        // Japon
        'JP' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Lettonie
        'LV' => [
            'amount' => 11.92,
            'currencyCode' => 'EUR'
        ],
        // Liban
        'LB' => [
            'amount' => 32.00,
            'currencyCode' => 'EUR'
        ],
        // Lituanie
        'LT' => [
            'amount' => 11.92,
            'currencyCode' => 'EUR'
        ],
        // Luxembourg
        'LU' => [
            'amount' => 7.42,
            'currencyCode' => 'EUR'
        ],
        // Malte
        'MT' => [
            'amount' => 11.92,
            'currencyCode' => 'EUR'
        ],
        // Martinique
        'MQ' => [
            'amount' => 20.83,
            'currencyCode' => 'EUR'
        ],
        // Mayotte
        'YT' => [
            'amount' => 20.83,
            'currencyCode' => 'EUR'
        ],
        // Mexique
        'MX' => [
            'amount' => 20.83,
            'currencyCode' => 'EUR'
        ],
        // Nouvelle zélande
        'NZ' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Pays-Bas
        'NL' => [
            'amount' => 7.42,
            'currencyCode' => 'EUR'
        ],
        // Philippines
        'PH' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Pologne
        'PL' => [
            'amount' => 10.42,
            'currencyCode' => 'EUR'
        ],
        // Portugal
        'PT' => [
            'amount' => 8.25,
            'currencyCode' => 'EUR'
        ],
        // Qatar
        'QA' => [
            'amount' => 32.00,
            'currencyCode' => 'EUR'
        ],
        // République Tchèque
        'CZ' => [
            'amount' => 10.42,
            'currencyCode' => 'EUR'
        ],
        // Réunion
        'RE' => [
            'amount' => 23.33,
            'currencyCode' => 'EUR'
        ],
        // Roumanie
        'RO' => [
            'amount' => 11.92,
            'currencyCode' => 'EUR'
        ],
        // Royaume Uni
        'GB' => [
            'amount' => 7.08,
            'currencyCode' => 'GBP'
        ],
        // Russie
        'RU' => [
            'amount' => 12.50,
            'currencyCode' => 'EUR'
        ],
        // Saint Barthélémy
        'BL' => [
            'amount' => 32.00,
            'currencyCode' => 'EUR'
        ],
        // Saint Martin
        'SM' => [
            'amount' => 32.00,
            'currencyCode' => 'EUR'
        ],
        // Saint-Martin (partie française)
        'MF' => [
            'amount' => 32.00,
            'currencyCode' => 'EUR'
        ],
        // Saint Pierre et Miquelon
        'PM' => [
            'amount' => 32.00,
            'currencyCode' => 'EUR'
        ],
        // Sainte Lucie
        'LC' => [
            'amount' => 32.00,
            'currencyCode' => 'EUR'
        ],
        // Seychelles
        'SC' => [
            'amount' => 32.00,
            'currencyCode' => 'EUR'
        ],
        // Singapour
        'SG' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Slovaquie
        'SK' => [
            'amount' => 11.92,
            'currencyCode' => 'EUR'
        ],
        // Slovénie
        'SI' => [
            'amount' => 11.92,
            'currencyCode' => 'EUR'
        ],
        // Sri Lanka
        'LK' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Suède
        'SE' => [
            'amount' => 9.08,
            'currencyCode' => 'EUR'
        ],
        // Taïwan
        'TW' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Thaïlande
        'TH' => [
            'amount' => 28.00,
            'currencyCode' => 'USD'
        ],
        // Tunisie
        'TN' => [
            'amount' => 28.00,
            'currencyCode' => 'EUR'
        ]
    ];

    /**
     * Add rate to DHL rates array
     *
     * @param SimpleXMLElement $shipmentDetails
     * @return Mage_Usa_Model_Shipping_Carrier_Dhl_International
     */
    protected function _addRate(SimpleXMLElement $shipmentDetails)
    {
        if (isset($shipmentDetails->ProductShortName) && 
            isset($shipmentDetails->ShippingCharge) && 
            isset($shipmentDetails->GlobalProductCode) && 
            array_key_exists(
                (string) $shipmentDetails->GlobalProductCode,
                $this->getAllowedMethods())
        ) {
            $shippingChargeInfo = $this->getShippingChargeInfo($shipmentDetails);
            if (!$shippingChargeInfo) {
                return $this;
            }
            // DHL product code, e.g. '3', 'A', 'Q', etc.
            $dhlProduct = (string) $shipmentDetails->GlobalProductCode;
            $totalEstimate = $shippingChargeInfo['amount'];
            $currencyCode = $shippingChargeInfo['currencyCode'];
            $baseCurrencyCode = Mage::app()->getWebsite($this->_request->getWebsiteId())->getBaseCurrencyCode();
            $dhlProductDescription = $this->getDhlProductTitle($dhlProduct);

            if ($currencyCode != $baseCurrencyCode) {
                /* @var $currency Mage_Directory_Model_Currency */
                $currency = Mage::getModel('directory/currency');
                $rates = $currency->getCurrencyRates($currencyCode, array($baseCurrencyCode));
                if (!empty($rates) && isset($rates[$baseCurrencyCode])) {
                    // Convert to store display currency using store exchange rate
                    $totalEstimate = $totalEstimate * $rates[$baseCurrencyCode];
                } else {
                    $rates = $currency->getCurrencyRates($baseCurrencyCode, array($currencyCode));
                    if (!empty($rates) && isset($rates[$currencyCode])) {
                        $totalEstimate = $totalEstimate / $rates[$currencyCode];
                    }
                    if (!isset($rates[$currencyCode]) || !$totalEstimate) {
                        $totalEstimate = false;
                        $this->_errors[] = Mage::helper('usa')->__("Exchange rate %s (Base Currency) -> %s not found. DHL method %s skipped", $currencyCode, $baseCurrencyCode, $dhlProductDescription);
                    }
                }
            }
            if ($totalEstimate) {
                $data = array(
                    'term' => $dhlProductDescription,
                    'price_total' => $this->getMethodPrice($totalEstimate, $dhlProduct)
                );
                if (!empty($this->_rates)) {
                    foreach ($this->_rates as $product) {
                        if ($product['data']['term'] == $data['term'] && 
                            $product['data']['price_total'] == $data['price_total']
                        ) {
                            return $this;
                        }
                    }
                }
                $this->_rates[] = array('service' => $dhlProduct, 'data' => $data);
            } else {
                $this->_errors[] = Mage::helper('usa')->__("Zero shipping charge for '%s'", $dhlProductDescription);
            }
        } else {
            $dhlProductDescription = false;
            if (isset($shipmentDetails->GlobalProductCode)) {
                $dhlProductDescription = $this->getDhlProductTitle((string) $shipmentDetails->GlobalProductCode);
            }
            $dhlProductDescription = $dhlProductDescription ? $dhlProductDescription : Mage::helper('usa')->__("DHL");
            $this->_errors[] = Mage::helper('usa')->__("Zero shipping charge for '%s'", $dhlProductDescription);
        }
        return $this;
    }

    /**
     * Get shipping charge info
     * @param SimpleXMLElement $shipmentDetails
     * @return array|null
     */
    protected function getShippingChargeInfo($shipmentDetails)
    {
        $shippingChargeInfo = [
            'amount' => (float)(string)$shipmentDetails->ShippingCharge,
            'currencyCode' => (string) $shipmentDetails->CurrencyCode,
        ];
        
        $destCountryCode = $this->_request->getDestCountryId();
        
        if ($destCountryCode &&
            isset($this->customRateInfo[$destCountryCode])
        ) {
            $customRateInfo = $this->customRateInfo[$destCountryCode];
            $shippingChargeInfo['amount'] = $customRateInfo['amount'];
            $shippingChargeInfo['currencyCode'] = $customRateInfo['currencyCode'];
            if ($destCountryCode === 'ES') {
                $destPostCode = $this->_request->getDestPostcode();
                for ($postCode = $customRateInfo['postCodeNotAllowed']['from']; $postCode <= $customRateInfo['postCodeNotAllowed']['to']; $postCode++) {
                    if (substr($destPostCode, 0, 2) == $postCode) {
                        return null;
                    }
                }
            }
        }
        
        return $shippingChargeInfo;
    }
}
