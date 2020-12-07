<?php

/*
cf : http://dev.turboweb.co.nz/2011/03/03/changes-to-customer-attributes/
*/

/*
	Ajoute les attributs dans la BDD
*/
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('customer', 'nb_order',
	array(
    'type' => 'int',
    'label' => 'Nb Commandes',
    'required' => 1,
    'is_visible' => 1,
	)
);

$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute('customer', 'nb_order');
$attribute->setData('used_in_forms', array(
    'customer_account_edit',
    'customer_account_create',
    'adminhtml_customer'
  )
);

$attribute->save();