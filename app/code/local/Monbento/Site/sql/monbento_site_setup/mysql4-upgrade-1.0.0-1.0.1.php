<?php

/* Ajout des attributs de produit */
$installerProduct = Mage::getResourceModel('catalog/setup', 'default_setup');

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_single', array(
    'group' => 'General',
    'type' => 'int',
    'input' => 'select',
    'label' => 'Ce produit bundle est un single',
    'backend' => '',
    'visible' => 1,
    'required' => false,
    'source' => 'eav/entity_attribute_source_boolean',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));