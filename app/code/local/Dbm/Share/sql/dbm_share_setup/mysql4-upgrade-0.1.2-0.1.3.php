<?php

$installer = $this;
$installer->startSetup();

$attributeCode = 'origin';

$installer->removeAttribute('order', $attributeCode);
/*
$attribute  = array(
    'type'            => 'int',
    'backend'         => '',
    'frontend'        => '',
    'label'           => 'Plateforme d\'origine',
    'is_user_defined' => true,
    'visible'         => false,
    'required'        => true,
    'user_defined'    => true,
    'searchable'      => true,
    'filterable'      => true,
    'comparable'      => true,
    'default'         => 0
);

$installer->addAttribute('order', 'origin', $attribute);
*/

$installer->getConnection()->addColumn($installer->getTable('sales/order'), $attributeCode, 'INT(11)');
$installer->endSetup();