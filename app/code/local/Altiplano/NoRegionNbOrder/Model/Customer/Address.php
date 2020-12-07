<?php

class Altiplano_NoRegionNbOrder_Model_Customer_Address extends Mage_Customer_Model_Address
{

    public function validate() {

        $errors = parent::validate();

				// It's ok so validate
				if ($errors === true) {
				    return true;
				}

				// Got an error is it state/province ?
				// try to remove it !
				$helper = Mage::helper('customer');
				$stateMsg = $helper->__('Please enter the state/province.');
				foreach($errors as &$error) {
						if ($error == $stateMsg) {
								unset($error);
						}
				}

				// It was state/province !!!
				if (count($error) == 0) {
						return true;
				}

        return $errors;
    }

}