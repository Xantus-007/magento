<?php

/* Ajout des attributs de produit */
$installerProduct = Mage::getResourceModel('catalog/setup', 'default_setup');

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'visuel_bandeau_perso', array(
    'group' => 'Images',
    'type' => 'varchar',
    'input' => 'media_image',
    'label' => 'Visuel du bandeau personnalisation',
    'frontend' => 'catalog/product_attribute_frontend_image',
    'visible' => 1,
    'required' => false,
    'source' => '',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installerProduct->endSetup();