<?php

$installer = $this;

$linkTable = $this->getTable('dbm_customer_link');
$query = 'ALTER TABLE `'.$linkTable.'` ADD `created_at` DATETIME  NOT NULL   AFTER `id_following`;';

$installer->run($query);

$installer->endSetup();