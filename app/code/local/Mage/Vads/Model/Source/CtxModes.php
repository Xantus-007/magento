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

class Mage_Vads_Model_Source_CtxModes
{
    public function toOptionArray()
    {
        $options =  array();

        foreach (Mage::getSingleton('vads/api_standard')->getConfigArray('ctx_mode') as $name => $code)
		{
            $options[] = array
			(
               'value' => $code,
               'label' => $name
            );
        }

        return $options;
    }
}