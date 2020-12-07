<?php

$setup = $this->startSetup();
$elementTable = $this->getTable('dbm_share_element');
$queries = array();

$queries[] = 'ALTER TABLE `'.$elementTable.'` MODIFY `type` VARCHAR(45) NOT NULL;';

foreach($queries as $query)
{
    $setup->run($query);
}

$setup->endSetup();