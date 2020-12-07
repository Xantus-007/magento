<?php

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
$installer->startSetup();

/* Ajout des attributs pour le post Faq */
$installer->addAttribute(Mageplaza_BetterBlog_Model_Post::ENTITY, 'image_mobile_retina', array(
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'image',
    'label' => 'Image adaptée mobile (150 dpi)',
    'backend' => 'mageplaza_betterblog/post_attribute_backend_image',
    'visible' => 1,
    'required' => false,
    'source' => '',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$entityTypeId = Mage::getModel('mageplaza_betterblog/post')->getResource()->getTypeId();
$attributeSetIdSlider = $this->getAttributeSetId($entityTypeId, 'Slider');
$this->addAttributeToSet($entityTypeId, $attributeSetIdSlider, 'General', 'image_mobile_retina');

$installer->endSetup();