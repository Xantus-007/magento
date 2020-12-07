<?php

/* Ajout/Suppression des attributs de catégorie */
$installerCategory = Mage::getResourceModel('catalog/setup', 'default_setup');

$installerCategory->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'baseline', array(
    'group' => 'General',
    'type' => 'text',
    'input' => 'textarea',
    'label' => 'Baseline',
    'backend' => '',
    'wysiwyg_enabled' => 1,
    'visible' => true,
    'required' => false,
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installerCategory->endSetup();
