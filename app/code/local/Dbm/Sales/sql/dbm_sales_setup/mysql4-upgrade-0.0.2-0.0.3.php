<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getTable('sales/order_item');
$installer->run("ALTER TABLE {$table} ADD COLUMN cost decimal(12,4) NULL;");

$installer->endSetup(); 