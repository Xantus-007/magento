<?php

$installer = $this;
/* @var $installer MageWorx_GeoIP_Model_Mysql4_Setup */
$installer->startSetup();
$table = $this->getTable('ustorelocator_location');
$conn = $installer->getConnection();
$conn->addColumn($table, 'photo', "text NOT NULL");
$installer->endSetup();
