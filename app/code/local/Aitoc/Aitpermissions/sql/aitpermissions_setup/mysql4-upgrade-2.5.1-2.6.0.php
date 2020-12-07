<?php
$installer = $this;

$installer->startSetup();

$installer->updateAttribute('catalog_product', 'created_by', 'is_visible', '1'); 
$installer->updateAttribute('catalog_product', 'created_by', 'source_model', 'aitpermissions/source_admins'); 
$installer->updateAttribute('catalog_product', 'created_by', 'frontend_label', 'Product owner'); 
$installer->updateAttribute('catalog_product', 'created_by', 'frontend_input', 'select'); 

$installer->endSetup();