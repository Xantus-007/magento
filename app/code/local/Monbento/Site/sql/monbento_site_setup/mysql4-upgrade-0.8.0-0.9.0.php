<?php

$entityTypeId = Mage::getModel('mageplaza_betterblog/post')->getResource()->getTypeId();
$installerProduct = Mage::getResourceModel('catalog/setup', 'default_setup');

/* Ajout des attributs pour le post Presse */
$installerProduct->addAttribute(Mageplaza_BetterBlog_Model_Post::ENTITY, 'display_title', array(
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Titre à afficher',
    'backend' => '',
    'is_visible' => 1,
    'required' => false,
    'is_user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
