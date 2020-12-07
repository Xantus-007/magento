<?php

$installer = $this;
$this->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'allowed_status', array(
    'type' => 'varchar',
    'label' => 'Statuts autorisés à accéder à la catégorie',
    'source' => 'dbm_customer/customer_attribute_source_category_status',
    'backend' => 'eav/entity_attribute_backend_array',
    'frontend' => '',
    'is_global' => false,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'input' => 'multiselect',
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'used_in_product_listing' => false,
    'default' => '',
));

$this->endSetup();