<?php

$installer = $this;

$attributeCodes = array('notification_date' => 'Date de dernière lecture des notifications');

$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

foreach($attributeCodes as $attributeCode => $label)
{
    /*
    $installer->removeAttribute('customer', $attributeCode);
    continue;
     */

    $installer->addAttribute('customer', $attributeCode, array(
        'type'              => 'datetime',
        'backend'           => '',
        'frontend'          => '',
        'label'             => $label,
        'input'             => 'date',
        'class'             => '',
        'source'            => '',
        'global'            => 0,
        'visible'           => 1,
        'required'          => 0,
        'user_defined'      => 0,
        'default'           => '0',
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
    $attribute->setData('used_in_forms', array('adminhtml_customer'));
    $attribute->save();
}
