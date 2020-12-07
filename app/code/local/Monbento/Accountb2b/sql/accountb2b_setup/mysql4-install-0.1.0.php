<?php

/*
cf : http://dev.turboweb.co.nz/2011/03/03/changes-to-customer-attributes/
*/

/*
	Ajoute les attributs dans la BDD
*/
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
// siret
$setup->addAttribute('customer', 'siret',
	array(
    	'type' => 'varchar',
    	'label' => 'Siret',
    	'required' => 0,
    	'is_visible' => 1,
    	'sort_order' => 100,
	)
);
// détenteur du compte
$setup->addAttribute('customer', 'detenteur_compte',
	array(
    	'type' => 'varchar',
    	'label' => 'Détenteur du compte',
    	'required' => 0,
    	'is_visible' => 1,
    	'sort_order' => 101,
	)
);
// rib valeur
$setup->addAttribute('customer', 'numero_de_compte',
	array(
    	'type' => 'varchar',
    	'label' => 'Numéro de compte',
    	'required' => 0,
    	'is_visible' => 1,
    	'sort_order' => 102,
	)
);
// Clé rib
$setup->addAttribute('customer', 'cle_rib',
	array(
    	'type' => 'varchar',
    	'label' => 'Clé de RIB',
    	'required' => 0,
    	'is_visible' => 1,
    	'sort_order' => 103,
	)
);
// Code banque
$setup->addAttribute('customer', 'code_banque',
	array(
    	'type' => 'varchar',
    	'label' => 'Code banque',
    	'required' => 0,
    	'is_visible' => 1,
    	'sort_order' => 104,
	)
);
// Iban
$setup->addAttribute('customer', 'iban',
	array(
    	'type' => 'varchar',
    	'label' => 'IBAN',
    	'required' => 0,
    	'is_visible' => 1,
    	'sort_order' => 105,
	)
);
// Bic
$setup->addAttribute('customer', 'bic',
	array(
    	'type' => 'varchar',
    	'label' => 'BIC',
    	'required' => 0,
    	'is_visible' => 1,
    	'sort_order' => 106,
	)
);
// paiement 30j
$setup->addAttribute('customer', 'paiement30j',
	array(
		'type' => 'int',
		'label' => 'Paiement à 30 jours',
		'input' => 'boolean',
		'required' => 0,
		'is_visible' => 1,
		'sort_order' => 107,
	)
);
// commentaire
$setup->addAttribute('customer', 'commentaire',
	array(
		'type' => 'text',
		'label' => 'Commentaire',
		'input' => 'textarea',
		'required' => 0,
		'is_visible' => 1,
		'sort_order' => 108,
	)
);


/*
// image rib/ibam
$setup->addAttribute('customer', 'rib',
	array(
		'type' => 'varchar',
		'label' => 'Rib/Iban',
		'input' => 'image',
		'required' => 0,
		'is_visible' => 1,
		'backend' => 'accountb2b/entity_attribute_backend_rib',
	)
);
// rib type
$setup->addAttribute('customer', 'ribtype',
	array(
		'type' => 'varchar',
		'label' => 'Type virement',
		'required' => 0,
		'is_visible' => 1,
		'sort_order' => 101,
		'source' => 'eav/entity_attribute_source_table',
		'input' => 'select',
		'option' => array(
			'value' => array(
				'rib' => array(0 => 'RIB'),
				'iban-bic' => array(0 => 'IBAN-BIC'),
			)
		),
	)
);
*/

/*
Ajoute les attributs aux form
*/

$eavConfig = Mage::getSingleton('eav/config');

$attributes = array(
	'siret',
	'detenteur_compte',
	'numero_de_compte',
	'cle_rib',
	'code_banque',
	'iban',
	'bic',
	'paiement30j',
	'commentaire'
);

foreach($attributes as $value) {
	$attribute = $eavConfig->getAttribute('customer', $value);
	$attribute->setData('used_in_forms',
		array(
    		'customer_account_edit',
    		'customer_account_create',
    		'adminhtml_customer',
    		//'checkout_register'
		)
	);
	$attribute->save();
}