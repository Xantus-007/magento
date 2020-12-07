<?php

/* @var $this Aitoc_Aitsys_Model_Mysql4_Setup */
$this->startSetup();

if(version_compare(Mage::getVersion(), '1.7.0.0', 'ge'))
{
    $this->getConnection()
    ->addColumn(
        $this->getTable('aitquantitymanager/stock_item'),
        'is_decimal_divided',
        array(
            'TYPE' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'LENGTH' => 5,
            'UNSIGNED' => true,
            'NULLABLE' => false,
            'DEFAULT' => 0,
            'COMMENT' => 'Is Divided into Multiple Boxes for Shipping'
        )
    ); 
}

$this->endSetup();