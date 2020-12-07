<?php
/**
 * DPD France shipping module for Magento
 *
 * @category   DPDFrance
 * @package    DPDFrance_Shipping
 * @author     Smile, Jibé, DPD France S.A.S. <ensavoirplus.ecommerce@dpd.fr>
 * @copyright  2015 DPD France S.A.S., société par actions simplifiée, au capital de 18.500.000 euros, dont le siège social est situé 27 Rue du Colonel Pierre Avia - 75015 PARIS, immatriculée au registre du commerce et des sociétés de Paris sous le numéro 444 420 830 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class DPDFrance_Export_Adminhtml_DPDFrance_ExportController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Constructor
     */
    protected function _construct()
    {        
        $this->setUsedModuleName('DPDFrance_Export');
    }

    /**
     * Main action : show orders list
     */
    public function indexAction() {
        $this->loadLayout()
            ->_setActiveMenu('sales/dpdfrexport/export')
            ->_addContent($this->getLayout()->createBlock('dpdfrexport/export_orders'))
            ->renderLayout();
    }
	
  	public static function getIsoCodebyIdCountry($idcountry) // Récupere le code ISO du pays concerné et le convertit au format attendu par la Station DPD
    {
		$isops = array("DE", "AD", "AT", "BE", "BA", "BG", "HR", "DK", "ES", "EE", "FI", "FR", "GB", "GR", "GG", "HU", "IM", "IE", "IT", "JE", "LV", "LI", "LT", "LU", "NO", "NL", "PL", "PT", "CZ", "RO", "RS", "SK", "SI", "SE", "CH");
		$isoep = array("D", "AND", "A", "B", "BA", "BG", "CRO", "DK", "E", "EST", "SF", "F", "GB", "GR", "GG", "H", "IM", "IRL", "I", "JE", "LET", "LIE", "LIT", "L", "N", "NL", "PL", "P", "CZ", "RO", "RS", "SK", "SLO", "S", "CH");
		
		if(in_array($idcountry, $isops)){ // Si le code ISO est européen, on le convertit au format Station DPD
			$code_iso = str_replace($isops, $isoep, $idcountry);
		}
		else{
			$code_iso = str_replace($idcountry, "INT", $idcountry); // Si le code ISO n'est pas européen, on le passe en "INT" (intercontinental)
		}
	return $code_iso;
    }
/**
     * Export Action
     * Generates a CSV file to download
     */
    public function exportAction() {
	    /* get the orders */
        $orderIds = $this->getRequest()->getPost('order_ids');

        /**
         * Get configuration
         */
        $delimiter = '';
        $lineBreak = "\r\n";
        
		// Le format .dat est requis pour la Station DPD. Le charset est ASCII
        $fileExtension = '.dat';
        $fileCharset = 'ISO-8859-1';
                
        /* set the filename */
        $filename   = 'DPDFRANCE_'.Mage::getSingleton('core/date')->date('dmY_His').$fileExtension;

        /* initialize the content variable */
        $content = '';

        if (!empty($orderIds)) {
        	
        	$content .= '$VERSION=110'.$lineBreak;
        	
            foreach ($orderIds as $orderId) {

	            /* get the order */
                $order = Mage::getModel('sales/order')->load($orderId);

                /* get the billing address */
                $address = $order->getShippingAddress();
                
            	/* total weight */
                $total_weight = $order->getWeight();
                // $items = $order->getAllItems();
                // foreach ($items as $item) {
                    // $total_weight += $item['weight'];
                // }
				
				/* type of delivery */
				$type = stristr($order->getShippingMethod(),'_', true);
                
				/* shipper code determination */
				switch ($type) {
					case 'dpdfrrelais' :
						$shipper_code = Mage::getStoreConfig('carriers/dpdfrrelais/cargo');
						break;
					case 'dpdfrpredict' :
						$shipper_code = Mage::getStoreConfig('carriers/dpdfrpredict/cargo');
						break;
					case 'dpdfrclassic' :
						$shipper_code = Mage::getStoreConfig('carriers/dpdfrclassic/cargo');
						break;
				}
				
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getRealOrderId(), 35); 				// Ref Commande Magento
	            $content = $this->_addFieldToCsv($content, $delimiter, '', 2); 										// Filler
                $content = $this->_addFieldToCsv($content, $delimiter, floor($order->getWeight()*100), 8, true );   // Poids
				$content = $this->_addFieldToCsv($content, $delimiter, '', 15);										// Filler
					$firstnamecleaned = $this->_stripAccents($address->getFirstname());
					$lastnamecleaned = $this->_stripAccents($address->getLastname());
				if($type !== 'dpdfrrelais'){
					$content = $this->_addFieldToCsv($content, $delimiter, $lastnamecleaned.' '.$firstnamecleaned, 35);	// Nom et prénom (Relais)
					$content = $this->_addFieldToCsv($content, $delimiter, '', 35);	
				} else {
                $content = $this->_addFieldToCsv($content, $delimiter, $lastnamecleaned, 35);						// Nom
                $content = $this->_addFieldToCsv($content, $delimiter, $firstnamecleaned, 35);}						// Prénom
					$addr2cleaned = $this->_stripAccents($address->getCompany());
                $content = $this->_addFieldToCsv($content, $delimiter, $addr2cleaned, 35);							// Complément d'adresse 2 : Nom du PR + ID ou société
					$addr3cleaned = $this->_stripAccents($address->getStreet(2));
                $content = $this->_addFieldToCsv($content, $delimiter, $addr3cleaned, 35);							// Complément d'adresse 3
					$addr3cleaned = $this->_stripAccents($address->getStreet(3));
                $content = $this->_addFieldToCsv($content, $delimiter, $addr3cleaned, 35);							// Complément d'adresse 4
					$addr4cleaned = $this->_stripAccents($address->getStreet(4));
                $content = $this->_addFieldToCsv($content, $delimiter, $addr4cleaned, 35);							// Complément d'adresse 5
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getPostcode(), 10);				// Code postal
					$city = $this->_stripAccents($address->getCity());												// Ville
                $content = $this->_addFieldToCsv($content, $delimiter, $city, 35);
	            $content = $this->_addFieldToCsv($content, $delimiter, '', 10);										// Filler
					$addr1cleaned = $this->_stripAccents($address->getStreet(1));									// Rue
                $content = $this->_addFieldToCsv($content, $delimiter, $addr1cleaned, 35);
	            $content = $this->_addFieldToCsv($content, $delimiter, '', 10);										// Filler
                $content = $this->_addFieldToCsv($content, $delimiter, $this->getIsoCodebyIdCountry($address->getCountry()), 3);			// Code pays
	            $content = $this->_addFieldToCsv($content, $delimiter, $address->getTelephone(), 30);				// Téléphone
	            $content = $this->_addFieldToCsv($content, $delimiter, '', 15);										// Filler
					$shippername = $this->_stripAccents(Mage::getStoreConfig('general/store_information/name'));
                $content = $this->_addFieldToCsv($content, $delimiter, $shippername, 35);           				// Nom expéditeur     
					$shipperstreet2 = $this->_stripAccents(Mage::getStoreConfig('shipping/origin/street_line2'));
                $content = $this->_addFieldToCsv($content, $delimiter, $shipperstreet2, 35);                		// Complément adresse 1
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);	 									// Filler
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler
					$shipperzipcode = $this->_stripAccents(Mage::getStoreConfig('shipping/origin/postcode'));			
                $content = $this->_addFieldToCsv($content, $delimiter, $shipperzipcode, 10);                		// Code postal
					$shippercity = $this->_stripAccents(Mage::getStoreConfig('shipping/origin/city'));					
                $content = $this->_addFieldToCsv($content, $delimiter, $shippercity, 35);                			// Ville
				$content = $this->_addFieldToCsv($content, $delimiter, '', 10);           							// Filler     	
				if (version_compare(Mage::getVersion(), '1.5.1', '>='))
					$shipperstreet = $this->_stripAccents(Mage::getStoreConfig('shipping/origin/street_line1'));
				else{
					$shipperstreet = explode(chr(10), $this->_stripAccents(Mage::getStoreConfig('general/store_information/address')));
					$shipperstreet = $shipperstreet[0];
				}
                $content = $this->_addFieldToCsv($content, $delimiter, $shipperstreet, 35);                			// Rue
				$content = $this->_addFieldToCsv($content, $delimiter, '', 10);										// Filler	                
                $content = $this->_addFieldToCsv($content, $delimiter, 'F', 3);										// Code Pays F                
					$shipperphone = Mage::getStoreConfig('general/store_information/phone');
                $content = $this->_addFieldToCsv($content, $delimiter, $shipperphone, 30);							// Telephone
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler	                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35); 									// Filler               	                
                $content = $this->_addFieldToCsv($content, $delimiter, date('d/m/Y'), 10);							// Date d'expédition théorique             
                $content = $this->_addFieldToCsv($content, $delimiter, $shipper_code, 8, true);            			// N° Compte chargeur    
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getRealOrderId(), 35);          		// Code barres
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getRealOrderId(), 35);          		// N° Commande
				$content = $this->_addFieldToCsv($content, $delimiter, '', 29);										// Filler	                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 9);										// Ad Valorem                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 8);										// Filler	                
                $content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Ref client 2	                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 1);										// Filler	                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 10); 									// Filler               
					$shipperemail = Mage::getStoreConfig('trans_email/ident_general/email');
                $content = $this->_addFieldToCsv($content, $delimiter, $shipperemail, 80); 							// E-mail expéditeur               
                $content = $this->_addFieldToCsv($content, $delimiter, '', 35); 									// GSM expéditeur               
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getCustomerEmail(), 80);				// E-mail destinataire
					$prefixe = substr($address->getTelephone(),0,2);
				if(($type !== 'dpdfrclassic' && $this->getIsoCodebyIdCountry($address->getCountry()) == 'F') && ($prefixe == 06 || $prefixe == 07))
					$content = $this->_addFieldToCsv($content, $delimiter, $address->getTelephone(), 35);
				else if($type == 'dpdfrclassic')
					$content = $this->_addFieldToCsv($content, $delimiter, $address->getTelephone(), 35);
				else
					$content = $this->_addFieldToCsv($content, $delimiter, '', 35);									// GSM destinataire               
				$content = $this->_addFieldToCsv($content, $delimiter, '', 96);										// Filler	                
				if($type == 'dpdfrrelais'){
					preg_match('/P\d{5}/i', $address->getCompany(), $relayId);
					$content = $this->_addFieldToCsv($content, $delimiter, $relayId[0], 8);}						// Id point relais
				else 
					$content = $this->_addFieldToCsv($content, $delimiter, '', 8); 
				$content = $this->_addFieldToCsv($content, $delimiter, '', 118);									// Filler	
	            /* Flag Predict */
				if($type == 'dpdfrpredict' && $address->getTelephone() && $this->getIsoCodebyIdCountry($address->getCountry()) == 'F')
					$content = $this->_addFieldToCsv($content, $delimiter,'+', 1);
				else
					$content = $this->_addFieldToCsv($content, $delimiter,'', 1);
                /* Nom du contact */
                $content = $this->_addFieldToCsv($content, $delimiter, $lastnamecleaned, 35);
				$content = $this->_addFieldToCsv($content, $delimiter, '', 30);										// Filler	
                $content .= $lineBreak;																				// CRLF
            }

            /* decode the content, depending on the charset */
            if ($fileCharset == 'ISO-8859-1') {
            	$content = utf8_decode($content);
            }

            /* pick file mime type, depending on the extension */
            if ($fileExtension == '.dat') {
            	$fileMimeType = 'text/plain';
            } else if ($fileExtension == '.csv') {
            	$fileMimeType = 'application/csv';
            } else {
            	// default
                $fileMimeType = 'text/plain';
            }

            /* download the file */
            return $this->_prepareDownloadResponse($filename, $content, $fileMimeType .'; charset="'. $fileCharset .'"');
        }
        else {
	        $this->_getSession()->addError($this->__('No Order has been selected'));
        }
    }
   
   /**
     * Export Action
     * Generates a CSV file to download
     */
    public function exportavAction() {
	    /* get the orders */
        $orderIds = $this->getRequest()->getPost('order_ids');

        /**
         * Get configuration
         */
        $delimiter = '';
        $lineBreak = "\r\n";
        
		// Le format .dat est requis pour la Station DPD. Le charset est ASCII
        $fileExtension = '.dat';
        $fileCharset = 'ISO-8859-1';
                
        /* set the filename */
        $filename   = 'DPDFRANCE_'.Mage::getSingleton('core/date')->date('Ymd_His').$fileExtension;

        /* initialize the content variable */
        $content = '';

        if (!empty($orderIds)) {
        	
        	$content .= '$VERSION=110'.$lineBreak;
        	
            foreach ($orderIds as $orderId) {

	            /* get the order */
                $order = Mage::getModel('sales/order')->load($orderId);

                /* get the billing address */
                $address = $order->getShippingAddress();
                
            	/* total weight */
                $total_weight = 0;
                $items = $order->getAllItems();
                foreach ($items as $item) {
                    $total_weight += $item['row_weight'];
                }
				
				/* type of delivery */
				$type = stristr($order->getShippingMethod(),'_', true);
                      				
				/* shipper code determination */
				switch ($type) {
					case 'dpdfrrelais' :
						$shipper_code = Mage::getStoreConfig('carriers/dpdfrrelais/cargo');
						break;
					case 'dpdfrpredict' :
						$shipper_code = Mage::getStoreConfig('carriers/dpdfrpredict/cargo');
						break;
					case 'dpdfrclassic' :
						$shipper_code = Mage::getStoreConfig('carriers/dpdfrclassic/cargo');
						break;
				}
				
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getRealOrderId(), 35); 				// Ref Commande Magento
	            $content = $this->_addFieldToCsv($content, $delimiter, '', 2); 										// Filler
                $content = $this->_addFieldToCsv($content, $delimiter, floor($total_weight*100), 8, true );			// Poids
				$content = $this->_addFieldToCsv($content, $delimiter, '', 15);										// Filler
					$firstnamecleaned = $this->_stripAccents($address->getFirstname());
					$lastnamecleaned = $this->_stripAccents($address->getLastname());
				if($type !== 'dpdfrrelais'){
					$content = $this->_addFieldToCsv($content, $delimiter, $lastnamecleaned.' '.$firstnamecleaned, 35);	// Nom et prénom (Relais)
					$content = $this->_addFieldToCsv($content, $delimiter, '', 35);	
				} else {
                $content = $this->_addFieldToCsv($content, $delimiter, $lastnamecleaned, 35);						// Nom
                $content = $this->_addFieldToCsv($content, $delimiter, $firstnamecleaned, 35);}						// Prénom
					$addr2cleaned = $this->_stripAccents($address->getCompany());
                $content = $this->_addFieldToCsv($content, $delimiter, $addr2cleaned, 35);							// Complément d'adresse 2 : Nom du PR + ID ou société
					$addr3cleaned = $this->_stripAccents($address->getStreet(2));
                $content = $this->_addFieldToCsv($content, $delimiter, $addr3cleaned, 35);							// Complément d'adresse 3
					$addr3cleaned = $this->_stripAccents($address->getStreet(3));
                $content = $this->_addFieldToCsv($content, $delimiter, $addr3cleaned, 35);							// Complément d'adresse 4
					$addr4cleaned = $this->_stripAccents($address->getStreet(4));
                $content = $this->_addFieldToCsv($content, $delimiter, $addr4cleaned, 35);							// Complément d'adresse 5
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getPostcode(), 10);				// Code postal
					$city = $this->_stripAccents($address->getCity());												// Ville
                $content = $this->_addFieldToCsv($content, $delimiter, $city, 35);
	            $content = $this->_addFieldToCsv($content, $delimiter, '', 10);										// Filler
					$addr1cleaned = $this->_stripAccents($address->getStreet(1));									// Rue
                $content = $this->_addFieldToCsv($content, $delimiter, $addr1cleaned, 35);
	            $content = $this->_addFieldToCsv($content, $delimiter, '', 10);										// Filler
                $content = $this->_addFieldToCsv($content, $delimiter, $this->getIsoCodebyIdCountry($address->getCountry()), 3);			// Code pays
	            $content = $this->_addFieldToCsv($content, $delimiter, $address->getTelephone(), 30);				// Téléphone
	            $content = $this->_addFieldToCsv($content, $delimiter, '', 15);										// Filler
					$shippername = $this->_stripAccents(Mage::getStoreConfig('general/store_information/name'));
                $content = $this->_addFieldToCsv($content, $delimiter, $shippername, 35);           				// Nom expéditeur     
					$shipperstreet2 = $this->_stripAccents(Mage::getStoreConfig('shipping/origin/street_line2'));
                $content = $this->_addFieldToCsv($content, $delimiter, $shipperstreet2, 35);                		// Complément adresse 1
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);	 									// Filler
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler
					$shipperzipcode = $this->_stripAccents(Mage::getStoreConfig('shipping/origin/postcode'));			
                $content = $this->_addFieldToCsv($content, $delimiter, $shipperzipcode, 10);                		// Code postal
					$shippercity = $this->_stripAccents(Mage::getStoreConfig('shipping/origin/city'));					
                $content = $this->_addFieldToCsv($content, $delimiter, $shippercity, 35);                			// Ville
				$content = $this->_addFieldToCsv($content, $delimiter, '', 10);           							// Filler     	
				if (version_compare(Mage::getVersion(), '1.5.1', '>='))
					$shipperstreet = $this->_stripAccents(Mage::getStoreConfig('shipping/origin/street_line1'));
				else{
					$shipperstreet = explode(chr(10), $this->_stripAccents(Mage::getStoreConfig('general/store_information/address')));
					$shipperstreet = $shipperstreet[0];
				}
                $content = $this->_addFieldToCsv($content, $delimiter, $shipperstreet, 35);                			// Rue
				$content = $this->_addFieldToCsv($content, $delimiter, '', 10);										// Filler	                
                $content = $this->_addFieldToCsv($content, $delimiter, 'F', 3);										// Code Pays F                
					$shipperphone = Mage::getStoreConfig('general/store_information/phone');
                $content = $this->_addFieldToCsv($content, $delimiter, $shipperphone, 30);							// Telephone
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler	                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35); 									// Filler               	                
                $content = $this->_addFieldToCsv($content, $delimiter, date('d/m/Y'), 10);							// Date d'expédition théorique             
                $content = $this->_addFieldToCsv($content, $delimiter, $shipper_code, 8, true);            			// N° Compte chargeur    
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getRealOrderId(), 35);               // Code barres
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getRealOrderId(), 35);          		// N° Commande
				$content = $this->_addFieldToCsv($content, $delimiter, '', 29);										// Filler	                
				$content = $this->_addFieldToCsv($content, $delimiter, substr($order->getGrandTotal(), 0, -2), 9, true);// Assurance Ad Valorem              
				$content = $this->_addFieldToCsv($content, $delimiter, '', 8);										// Filler	                
                $content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Ref client 2	                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 1);										// Filler	                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 35);										// Filler                
				$content = $this->_addFieldToCsv($content, $delimiter, '', 10); 									// Filler               
					$shipperemail = Mage::getStoreConfig('trans_email/ident_general/email');
                $content = $this->_addFieldToCsv($content, $delimiter, $shipperemail, 80); 							// E-mail expéditeur               
                $content = $this->_addFieldToCsv($content, $delimiter, '', 35); 									// GSM expéditeur               
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getCustomerEmail(), 80);				// E-mail destinataire
					$prefixe = substr($address->getTelephone(),0,2);
				if(($type !== 'dpdfrclassic' && $this->getIsoCodebyIdCountry($address->getCountry()) == 'F') && ($prefixe == 06 || $prefixe == 07))
					$content = $this->_addFieldToCsv($content, $delimiter, $address->getTelephone(), 35);
				else if($type == 'dpdfrclassic')
					$content = $this->_addFieldToCsv($content, $delimiter, $address->getTelephone(), 35);
				else
					$content = $this->_addFieldToCsv($content, $delimiter, '', 35);									// GSM destinataire                 
				$content = $this->_addFieldToCsv($content, $delimiter, '', 96);										// Filler	                
				if($type == 'dpdfrrelais'){
					preg_match('/P\d{5}/i', $address->getCompany(), $relayId);
					$content = $this->_addFieldToCsv($content, $delimiter, $relayId[0], 8);}						// Id point relais
				else 
					$content = $this->_addFieldToCsv($content, $delimiter, '', 8); 
				$content = $this->_addFieldToCsv($content, $delimiter, '', 118);									// Filler	
	            /* Flag Predict */
				if($type == 'dpdfrpredict' && $address->getTelephone() && $this->getIsoCodebyIdCountry($address->getCountry()) == 'F')
					$content = $this->_addFieldToCsv($content, $delimiter,'+', 1);
				else
					$content = $this->_addFieldToCsv($content, $delimiter,'', 1);
                /* Nom du contact */
                $content = $this->_addFieldToCsv($content, $delimiter, $lastnamecleaned, 35);
				$content = $this->_addFieldToCsv($content, $delimiter, '', 30);										// Filler	
                $content .= $lineBreak;																				// CRLF
            }

            /* decode the content, depending on the charset */
            if ($fileCharset == 'ISO-8859-1') {
            	$content = utf8_decode($content);
            }

            /* pick file mime type, depending on the extension */
            if ($fileExtension == '.dat') {
            	$fileMimeType = 'text/plain';
            } else if ($fileExtension == '.csv') {
            	$fileMimeType = 'application/csv';
            } else {
            	// default
                $fileMimeType = 'text/plain';
            }

            /* download the file */
            return $this->_prepareDownloadResponse($filename, $content, $fileMimeType .'; charset="'. $fileCharset .'"');
        }
        else {
	        $this->_getSession()->addError($this->__('No Order has been selected'));
        }
    }
	/**
     * Add a new field to the csv file
     * @param csvContent : the current csv content
     * @param fieldDelimiter : the delimiter character
     * @param fieldContent : the content to add
     * @return : the concatenation of current content and content to add
     */
    private function _addFieldToCsv($csvContent, $fieldDelimiter, $fieldContent, $size = 0, $isNum = false) {
    	if( !$size ) {
	    	return $csvContent . $fieldDelimiter . $fieldContent . $fieldDelimiter;
    	} else {
    		$newFieldContent = $fieldContent;
    		if( $isNum ) {
    			for($i=strlen($fieldContent);$i<$size;$i++) {
    				$newFieldContent = '0'.$newFieldContent;
    			}
    		} else {
    			for($i=strlen($fieldContent);$i<$size;$i++) {
    				$newFieldContent .= ' ';
    			}
    		}
    		/*if( strlen( $newFieldContent ) != $size ) {
    			var_dump('!! FAIL !! '. $newFieldContent);
    		}*/
    		$newFieldContent = substr( $newFieldContent, 0, $size );
    		return $csvContent . $fieldDelimiter . $newFieldContent . $fieldDelimiter;
    	}
    }
	// Fonction pour enlever les accents afin d'eliminer les erreurs d'encodage
	private function _stripAccents($str){
		$str = preg_replace('/[\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}]/u','A', $str);
		$str = preg_replace('/[\x{0105}\x{0104}\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}]/u','a', $str);
		$str = preg_replace('/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}]/u','C', $str);
		$str = preg_replace('/[\x{00E7}\x{0107}\x{0109}\x{010B}\x{010D}}]/u','c', $str);
		$str = preg_replace('/[\x{010E}\x{0110}]/u','D', $str);
		$str = preg_replace('/[\x{010F}\x{0111}]/u','d', $str);
		$str = preg_replace('/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}]/u','E', $str);
		$str = preg_replace('/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}]/u','e', $str);
		$str = preg_replace('/[\x{00CC}\x{00CD}\x{00CE}\x{00CF}\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}]/u','I', $str);
		$str = preg_replace('/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}]/u','i', $str);
		$str = preg_replace('/[\x{0142}\x{0141}\x{013E}\x{013A}]/u','l', $str);
		$str = preg_replace('/[\x{00F1}\x{0148}]/u','n', $str);
		$str = preg_replace('/[\x{00D2}\x{00D3}\x{00D4}\x{00D5}\x{00D6}\x{00D8}]/u','O', $str);
		$str = preg_replace('/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}]/u','o', $str);
		$str = preg_replace('/[\x{0159}\x{0155}]/u','r', $str);
		$str = preg_replace('/[\x{015B}\x{015A}\x{0161}]/u','s', $str);
		$str = preg_replace('/[\x{00DF}]/u','ss', $str);
		$str = preg_replace('/[\x{0165}]/u','t', $str);
		$str = preg_replace('/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{016E}\x{0170}\x{0172}]/u','U', $str);
		$str = preg_replace('/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{016F}\x{0171}\x{0173}]/u','u', $str);
		$str = preg_replace('/[\x{00FD}\x{00FF}]/u','y', $str);
		$str = preg_replace('/[\x{017C}\x{017A}\x{017B}\x{0179}\x{017E}]/u','z', $str);
		$str = preg_replace('/[\x{00C6}]/u','AE', $str);
		$str = preg_replace('/[\x{00E6}]/u','ae', $str);
		$str = preg_replace('/[\x{0152}]/u','OE', $str);
		$str = preg_replace('/[\x{0153}]/u','oe', $str);
		$str = preg_replace('/[\x{0022}\x{0025}\x{0026}\x{0027}\x{00B0}]/u',' ', $str);
		return $str;
	}
}


