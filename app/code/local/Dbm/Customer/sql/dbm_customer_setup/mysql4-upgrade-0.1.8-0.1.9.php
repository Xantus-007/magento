<?php 

$installer = $this;
$installer->startSetup();

//Delete older attribute
$installer->removeAttribute('customer', 'photo_points');
$installer->removeAttribute('customer', 'receipe_points');
$installer->removeAttribute('customer', 'points_photo');
$installer->removeAttribute('customer', 'points_receipe');
$installer->removeAttribute('customer', 'points_other');

//Add points status attribute
$attributeCodes = array(
    'points_photo' => 'Photo points',
    'points_receipe' => 'Receipe Points',
    'points_other' => 'Other points'
);

$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

foreach($attributeCodes as $attributeCode => $label)
{
    $installer->addAttribute('customer', $attributeCode, array(
        'type'              => 'decimal',
        'backend'           => '',
        'frontend'          => '',
        'label'             => $label,
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
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
    $attribute->setData('used_in_forms', array('adminhtml_customer'));
    $attribute->save();
}

$installer->endSetup();