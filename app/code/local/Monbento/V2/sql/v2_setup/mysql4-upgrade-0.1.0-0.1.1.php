<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('customer', 'fiscal_id', array(
    'type' => 'varchar',
    'label' => 'Numéro d\'identification fiscale',
    'required' => 0,
    'is_visible' => 1,
    'sort_order' => 110,
));


$eavConfig = Mage::getSingleton('eav/config');

$attributes = array(
    'fiscal_id'
);

foreach ($attributes as $value) {
    $attribute = $eavConfig->getAttribute('customer', $value);
    $attribute->setData('used_in_forms', array(
        'customer_account_edit',
        'customer_account_create',
        'adminhtml_customer',
        'checkout_register'
    ));
    $attribute->save();
}