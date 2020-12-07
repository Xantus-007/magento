<?php 

$setup = $this->startSetup();

$queries = array();
$catTable = $this->getTable('dbm_share_category');

$queries[] = 'ALTER TABLE `'.$catTable.'` ADD `position` INT NOT NULL DEFAULT "0" AFTER `image`';

foreach($queries as $query)
{
    $setup->run($query);
}

$setup->endSetup();