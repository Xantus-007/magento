<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
->addColumn($installer->getTable('sales/order'), 'frais_livraison_reel', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'nullable'  => true,
    'length'    => 50,
    'after'     => null,
    'comment'   => 'Frais de livraison réel'
    ));
$installer->getConnection()
->addColumn($installer->getTable('sales/order'), 'type_sav', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'nullable'  => true,
    'length'    => 50,
    'after'     => null,
    'comment'   => 'Type SAV / echantillon'
    )); 
$installer->endSetup();