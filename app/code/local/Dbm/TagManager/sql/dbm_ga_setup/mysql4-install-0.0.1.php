<?php

$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');

$installer->addAttribute('order', 'ga_client_id', array(
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'GA Client ID',
    'input'             => 'text',
    'class'             => '',
    'source'            => '',
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
));

$installer->endSetup();