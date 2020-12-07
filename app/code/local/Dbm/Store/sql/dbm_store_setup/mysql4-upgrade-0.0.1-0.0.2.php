<?php

$installer = $this;

$table = $this->getTable('ustorelocator_location');
$conn = $installer->getConnection();
$conn->addColumn($table, 'type', "int(11) NOT NULL DEFAULT 0");

$connexion = Mage::getSingleton('core/resource')->getConnection('core_write');
$connexion->resetDdlCache();

$installer->endSetup();