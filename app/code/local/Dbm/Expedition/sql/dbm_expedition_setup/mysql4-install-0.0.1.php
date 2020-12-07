<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
        ->addColumn($installer->getTable('sales/order'), 'sender_admin_id', array(
            'TYPE' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'NULLABLE'  => false,
            'DEFAULT'   => '0',
            'COMMENT' => 'Sender Admin Id'
        ));

$installer->endSetup();


$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');

$installer->addAttribute('order', 'sender_admin_id', array(
    'type'              => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Sender Admin Id',
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