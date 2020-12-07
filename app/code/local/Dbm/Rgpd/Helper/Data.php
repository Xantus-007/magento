<?php

class Dbm_Rgpd_Helper_Data extends Mage_Core_Helper_Abstract
{

	const DATA_POLICY_URL = 'politique-de-donnees';

	public function getMessage($type){
		$dataPolicyUrl = Mage::getUrl(self::DATA_POLICY_URL);

		switch ($type) {
			case 'newsletter_account':
				return $this->__("I agree to receive newsletters from monbento. I can unsubscribe at any time");
			case 'newsletter_footer':
				return $this->__("I agree to receive newsletters from monbento and that my data will be processed in accordance with monbento's <a href='%s' target='_blank'>personal data management policy</a>. I can unsubscribe at any time.", $dataPolicyUrl);
			case 'contact':
				return $this->__("By submitting this form I accept that the information entered will be used to provide an answer by mail or by phone to my request.");
			case 'contact_policy':
				return $this->__("To know and exercise your rights concerning your data, please consult our <a href='%s' target='_blank'>Personal data management policy</a>.", $dataPolicyUrl);
			case 'register_newsletter':
				return $this->__("I agree to receive newsletters from monbento. I can unsubscribe at any time");
			case 'register_policy':
				return $this->__("By clicking on \"Validate\", I agree that my personal data may be processed in accordance with monbento's <a href='%s' target='_blank'>personal data management policy</a>.", $dataPolicyUrl);
		}
	}

}
