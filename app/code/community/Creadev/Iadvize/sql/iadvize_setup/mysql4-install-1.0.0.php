<?php

$installer = $this;

// Partie CMS
$installer->startSetup();
$installer->run("ALTER TABLE `cms_page` ADD `iadvize` BOOL NOT NULL DEFAULT '0';");
$installer->endSetup();

// Partie Catégorie
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$setup->addAttribute('catalog_category', 'iadvize', array(
    'group'         => 'General',
    'input'         => 'select',
    'source'				=> 'eav/entity_attribute_source_boolean',
    'type'          => 'int',
    'label'         => 'Iadvize',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$setup->addAttribute('catalog_product', 'iadvize', array(
    'group'         => 'General',
    'input'         => 'select',
    'source'				=> 'eav/entity_attribute_source_boolean',
    'type'          => 'int',
    'label'         => 'Iadvize',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$installer->endSetup();