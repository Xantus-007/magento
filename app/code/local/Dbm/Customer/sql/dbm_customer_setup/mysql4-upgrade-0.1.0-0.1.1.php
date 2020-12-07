<?php
$installer = $this;

$attributes = array(
    'profile_nickname',
    'profile_image',
    'profile_is_vip'
);

//Create customer folder
$path = Mage::getBaseDir('media').DS.Dbm_Customer_Helper_Data::MEDIA_FOLDER;
$io = new Varien_Io_File();
$io->setAllowCreateFolders(true);
$io->createDestinationDir($path);

$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

//Nickname
$installer->addAttribute('customer', $attributes[0], array(
    'type'              => 'text',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Profile nickname',
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

//Image
$installer->addAttribute('customer', $attributes[1], array(
    'type'              => 'varchar',
    'backend'           => 'dbm_customer/attribute_backend_image',
    'frontend'          => '',
    'label'             => 'Profile image',
    'input'             => 'image',
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

//Status
$installer->addAttribute('customer', $attributes[2], array(
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Profile VIP',
    'input'             => 'boolean',
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

foreach($attributes as $attributeCode)
{
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
