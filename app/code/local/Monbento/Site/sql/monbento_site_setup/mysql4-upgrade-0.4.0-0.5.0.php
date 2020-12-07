<?php

/* Ajout des attributs de produit */
$installerProduct = Mage::getResourceModel('catalog/setup', 'default_setup');

$installerProduct->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'texte_bandeau_perso_bas', array(
    'group'         => 'Mise en page Monbento',
    'type'          => 'text',
    'input'         => 'textarea',
    'default'       => '',
    'label'         => 'Texte du bandeau de personnalisation sous le pinceau',
    'backend'       => '',
    'visible'    => 1,
    'required'      => false,
    'wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'html_allowed_on_front' => 1,
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerProduct->endSetup();