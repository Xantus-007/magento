<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
        ->addColumn($installer->getTable('sales/order_aggregated_updated'), 'total_base_cost', array(
            'TYPE' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'SCALE' => 4,
    		'PRECISION' => 12,
            'NULLABLE'  => false,
            'DEFAULT'   => '0.0000',
            'COMMENT' => 'Total Base Cost'
        ));

$installer->endSetup();

