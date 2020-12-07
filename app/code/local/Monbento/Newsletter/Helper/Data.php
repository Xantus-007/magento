<?php

include(dirname(dirname(__FILE__)) . "/Model/Mailjet.php");

class Monbento_Newsletter_Helper_Data extends Mage_Core_Helper_Abstract 
{

	function listContacts()
	{

		$apiKey = Mage::getStoreConfig('newsletter/mailjet/apikey');
        $secretKey = Mage::getStoreConfig('newsletter/mailjet/secretkey');
             
        $contacts = array(
        	array(
        		'value' => '',
        		'label' => '',
        	)
        );

		try {

			$mj = new Mailjet( $apiKey, $secretKey );

			$params = array(
		        "method" => "GET",
		        "Limit" =>  "300"
		    );
		    $result = $mj->contactslist($params);

		    if ($mj->_response_code == 200){
		    	if(isset($result->Data) && $result->Data){
		    		foreach ($result->Data as $contact) {
			            $contacts[] = array(
			            	'value' => $contact->ID,
			            	'label' => $contact->Name,
			            );
			        }
		    	}
		    }
		} catch (Exception $e) {
			Mage::log($e->getMessage(),null,'mailjet.log',true);
		}

	    return $contacts;
	}

	function addContactToList($email, $listID)
	{
		$apiKey = Mage::getStoreConfig('newsletter/mailjet/apikey');
        $secretKey = Mage::getStoreConfig('newsletter/mailjet/secretkey');

        try {
		    $params = array(
		        "method" => "POST",
		        "Email" => $email,
		    );

		    $mj = new Mailjet( $apiKey, $secretKey );
		    $result = $mj->contact($params);

		    if ($mj->_response_code == 201){
		    	if(isset($result->Data[0]->ID)){
			    	$contactID = (int) $result->Data[0]->ID;
			    	$params = array(
				        "method" => "POST",
				        "ContactID" => $contactID,
				        "ListID" => $listID,
				        "IsActive" => "True"
				    );

				    $result = $mj->listrecipient($params);
				    if ($mj->_response_code == 201){
				    	return true;
				    }
				}
		    }
		} catch (Exception $e) {
			Mage::log($e->getMessage(),null,'mailjet.log',true);
		}
	    
	    return false;
	}

}