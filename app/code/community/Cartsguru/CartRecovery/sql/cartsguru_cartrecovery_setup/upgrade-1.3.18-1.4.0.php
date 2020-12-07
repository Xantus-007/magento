<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

//generate flag for quote queue
$connection->addColumn($this->getTable('sales/quote'), 'in_cartsguru_queue', 'SMALLINT(5) NOT NULL');
$connection->addColumn($this->getTable('sales/quote'), 'cartsguru_source', 'VARCHAR(65535) NOT NULL');

//generate flag for order queue
$connection->addColumn($this->getTable('sales/order'), 'in_cartsguru_queue', 'SMALLINT(5) NOT NULL');
$connection->addColumn($this->getTable('sales/order'), 'cartsguru_source', 'VARCHAR(65535) NOT NULL');

//generate flag for customer queue
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$attribute  = array(
    "type"     => "int",
    "backend"  => "",
    "label"    => "",
    "input"    => "boolean",
    "source"   => "",
    "visible"  => false,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""
);
$setup->addAttribute('customer', 'in_cartsguru_queue', $attribute);

$installer->endSetup();
