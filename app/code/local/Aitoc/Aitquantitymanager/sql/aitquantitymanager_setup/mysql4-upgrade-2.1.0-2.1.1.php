<?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 */

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

if (version_compare(Mage::getVersion(), '1.6.0.0', 'ge'))
{
    $installer->startSetup();
    $connection = $installer->getConnection();
    $table = $this->getTable('aitquantitymanager/stock_item');

    if($connection->tableColumnExists($table, 'stock_status_changed_automatically')) {
        $installer->run("
        ALTER TABLE {$table} CHANGE `stock_status_changed_automatically` `stock_status_changed_auto` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0';
        ");
    }
    $installer->endSetup();
}