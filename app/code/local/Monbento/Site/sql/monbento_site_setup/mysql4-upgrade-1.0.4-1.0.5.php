<?php

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
$installer->startSetup();

/* Ajout des attributs pour le post Faq */
$installer->addAttribute(Mageplaza_BetterBlog_Model_Post::ENTITY, 'yt_video_id', array(
    'group' => 'General',
    'type' => 'text',
    'input' => 'text',
    'label' => 'Identifiant Vidéo Youtube',
    'backend' => '',
    'is_visible' => 1,
    'required' => false,
    'is_user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'note' => 'Seulement pour un slider de type vidéo',
));

$entityTypeId = Mage::getModel('mageplaza_betterblog/post')->getResource()->getTypeId();
$attributeSetIdSlider = $this->getAttributeSetId($entityTypeId, 'Slider');
$this->addAttributeToSet($entityTypeId, $attributeSetIdSlider, 'General', 'yt_video_id');

$installer->endSetup();