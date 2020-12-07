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

class Mage_Vads_Model_Field_CreditCards extends Mage_Core_Model_Config_Data {

	public function save() {
		$msg = Mage::helper('vads')->__("Invalid value for field ") . '"' . Mage::helper('vads')->__("Credit Card Types") . '"';
		
		$value = $this->getValue(); 

		if(!preg_match('#^([A-Z0-9-_]+;)*[A-Z0-9-_]*$#', $value)) {
			throw new Mage_Core_Exception($msg);
		} 
		
		return parent::save();
	}
	
}

?>