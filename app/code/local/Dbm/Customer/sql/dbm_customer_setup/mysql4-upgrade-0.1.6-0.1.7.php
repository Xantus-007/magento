<?php

$installer = $this;

$attributeCode = 'profile_url';

//$installer->removeAttribute('customer', 'profile_is_vip');

$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('customer', 'profile_url', array(
    'type'              => 'text',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Profile url',
    'input'             => 'text',
    'class'             => '',
    'source'            => 'dbm_customer/customer_attribute_source_status',
    'global'            => 0,
    'visible'           => 1,
    'required'          => 0,
    'user_defined'      => 0,
    'default'           => '',
    'searchable'        => 0,
    'filterable'        => 0,
    'comparable'        => 0,
    'visible_on_front'  => 0,
    'unique'            => 0,
    'position'          => 1,
));

$installer->addAttributeToGroup(
     $entityTypeId,
     $attributeSetId,
     $attributeGroupId,
     $attributeCode,
     '999'  //sort_order
);

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
$attribute->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'));
$attribute->save();

$installer->endSetup();