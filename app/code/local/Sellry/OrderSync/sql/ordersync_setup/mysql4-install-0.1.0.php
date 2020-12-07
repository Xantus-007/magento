<?php
/**
 * The Magento Developer
 * http://themagentodeveloper.com
 *
 * @category   Sellry
 * @package    Sellry_OrderSync
 * @version    0.1.0
 */

    $installer = $this;
    $installer->startSetup();

    $orderTable = $installer->getTable('sales/order');
    
    try {
        $installer->run("
                ALTER TABLE `{$orderTable}` ADD `sync_status` VARCHAR (50) NULL DEFAULT 'not_set';
        ");
    } catch (Exception $e) { }
    
    $installer->endSetup();