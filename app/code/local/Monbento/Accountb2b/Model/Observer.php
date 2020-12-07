<?php

class Monbento_Accountb2b_Model_Observer {

    public function eventSave($observer) {

        $Controller = $observer->getControllerAction();
        //si notre Controller correspond bien au controlller qui traite l'enregistrement
        if ($Controller instanceof Mage_Customer_AccountController) {
            $actionName = $Controller->getFullActionName();
            // l'action qui traite l'enregistrement d'un nouveu cleint
            if ($actionName == 'customer_account_createpost' && constant('Z_STORE_TYPE') == 'B2B') {

                // récuperer les informations envoyé par le formulaire
                $data = $Controller->getRequest()->getPost();
                $dataEmail = $data;
                
                // Load le client
                $customerId = Mage::getSingleton('customer/session')->getId();
                $customer   = Mage::getModel('customer/customer')->load($customerId);
                
                // enregistre l'adresse de shipping (cf: AccountController.php);
                unset($data['street'], $data['postcode'], $data['city'], $data['country_id']);
                foreach($data['shipping'] as $key => $value) {
                	$data[$key] = $value;
                }
                unset($data['shipping']);
                
                
                $address = Mage::getModel('customer/address');
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')->setEntity($address);

                $addressData   = $addressForm->extractData($addressForm->prepareRequest($data));
                $addressErrors = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)
		                    ->setSaveInAddressBook(true)
                        	->setIsDefaultBilling(false)
                        	->setIsDefaultShipping(true);
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);
                    
                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
							}

							$customer->setFirstname($data['firstname']);
							$customer->save();
							$this->_sendEmailToOwner($customerId, $dataEmail);
            }
        }
    }
    
    protected function _sendEmailToOwner($id, $data) {
    		$nl = "\n";
    
        $fromEmail = "sitepro@monbento.com"; // sender email address
    		$fromName = "Site Pro monbento.com"; // sender name
 
    		$toEmail = Mage::getStoreConfig('contacts/email/recipient_email'); // recipient email address

    		$body = "Body"; // body text
    		$subject = "Nouvelle inscription d'un professionnal - " . $data['company']; // subject text
				$body =  "Bonjour,".$nl
								. $nl
								. "Un nouveau professionnel vient de s'inscrire sur le site pro.monbento.com". $nl
								.	"Son compte est pour le moment dans l'attente d'une activation.". $nl
								. $nl
								. $nl
								. "=== Voici le récpitulatif de son compte :" . $nl
								. $nl
								. (!empty($data['firstname']) 				? "Prénom: " . $data['firstname'] . $nl : '')
								. (!empty($data['lastname']) 				? "Nom: ". $data['lastname'] . $nl : '')
								. (!empty($data['company'])					? "Société: " . $data['company'] . $nl : '')
								. (!empty($data['telephone'])				? "Téléphone: " . $data['telephone'] . $nl : '')
								. (!empty($data['email']) 						? "Email: " . $data['email'] . $nl : '')
								. (!empty($data['siret']) 						? "Siret: " . $data['siret'] . $nl : '')
								. (!empty($data['detenteur_compte']) ? "Détenteur du compte: " . $data['detenteur_compte'] . $nl : '')
								. (!empty($data['numero_de_compte']) ? "Numéro de compte: " . $data['numero_de_compte'] . $nl : '')
								. (!empty($data['cle_rib']) 					? "Clé RIB: " . $data['cle_rib'] . $nl : '')
								. (!empty($data['code_banque']) 			? "Code banque: " . $data['code_banque'] . $nl : '')
								. (!empty($data['iban']) 						? "IBAN: " . $data['iban'] . $nl : '')
							  . (!empty($data['bic'])							? "BIC: " . $data['bic'] . $nl : '')
							  . (!empty($data['taxvat'])						? "N° TVA: " . $data['taxvat'] . $nl : '')
							  . (!empty($data['commentaire'])			? "Commentaire: " . $data['commentaire'] . $nl : '')
							  . $this->_formatAddress('Adresse de facturation:', array(
							  		'street' 		 =>$data['street'],
							  		'postcode' 	 => $data['postcode'],
							  		'city' 			 => $data['city'],
							  		'country_id' => $data['country_id']))
							  . $this->_formatAddress('Adresse de livraison:', $data['shipping']);
							  
    		$mail = new Zend_Mail('utf-8');
    		$mail->setBodyText($body);
 				$mail->setFrom($fromEmail, $fromName);
    		$mail->addTo($toEmail);
 				$mail->setSubject($subject);
 
    		try {
        	$mail->send();
    		}
    		catch(Exception $ex) {
        	Mage::getSingleton('core/session')
            	->addError(Mage::helper('accountb2b')
            	->__('Unable to send email.'));
    		}
    }
    
    protected function _formatAddress($type, $address) {
    	$nl = "\n";
    	
			$rtn = $nl
						. $type . $nl
						. (is_array($address['street']) 	? implode($nl, $address['street']) . $nl : '')
						. (!empty($address['postcode']) 		? $address['postcode'] . $nl : '')
						. (!empty($address['city']) 				? $address['city'] . $nl : '')
						. (!empty($address['country_id']) 	? $address['country_id'] . $nl : '');
						
			return $rtn;
    }
}