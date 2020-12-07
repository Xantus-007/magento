<?php

$installer = $this;
$attributes = array('profile_nickname', 'profile_image');

foreach($attributes as $attributeCode)
{
    $attribute = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
    $attribute->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'));
    $attribute->save();
}

$installer->endSetup();