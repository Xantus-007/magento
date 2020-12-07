<?php

/* Ajout des attributs de catégorie */
$installerCategory = Mage::getResourceModel('catalog/setup', 'default_setup');

$installerCategory->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'description_bas', array(
    'group' => 'General',
    'type' => 'text',
    'input' => 'textarea',
    'label' => 'Description longue (bas de page)',
    'backend' => '',
    'visible' => true,
    'required' => false,
    'source' => '',
    'wysiwyg_enabled' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerCategory->endSetup();