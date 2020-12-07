<?php

$elementTable = $this->getTable('dbm_share_element');

$setup = $this->startSetup();
$query = 'ALTER TABLE `'.$elementTable.'` ADD `show_in_home` INT NULL AFTER `price`;';
$setup->run($query);


$setup->endSetup();
