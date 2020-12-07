<?php
/**
* @copyright  Copyright (c) 2012 AITOC, Inc. 
*/

$installer = $this;

$installer->startSetup();

$installer->run($sql = "
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoc_aitpermissions_approvedproducts')} (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL,
  `is_approved` smallint(1) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
    
");

$installer->endSetup();