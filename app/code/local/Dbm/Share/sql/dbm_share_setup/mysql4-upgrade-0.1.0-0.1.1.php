<?php 

$installer = $this;
$catTable = $this->getTable('dbm_share_category');
$queries = array();

$queries[] = 'ALTER TABLE `'.$catTable.'` ADD `image2` VARCHAR(45) NULL AFTER `image`';

foreach($queries as $query)
{
    $installer->run($query);
}

$installer->endSetup();