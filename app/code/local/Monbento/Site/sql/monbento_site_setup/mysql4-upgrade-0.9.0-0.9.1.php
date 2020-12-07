<?php

/* Ajout des attributs de produit */
$installerProduct = Mage::getResourceModel('catalog/setup', 'default_setup');

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'select_shop_category', array(
    'group' => 'General',
    'type' => 'int',
    'input' => 'select',
    'label' => 'Choix du bloc de la page shop pour afficher ce produit',
    'backend' => '',
    'visible' => 1,
    'required' => false,
    'source' => 'monbento_site/attribute_source_shopscategory',
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));