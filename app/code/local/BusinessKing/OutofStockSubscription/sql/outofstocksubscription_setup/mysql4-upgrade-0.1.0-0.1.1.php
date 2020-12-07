<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('outofstocksubscription_info')} 
    ADD `store_id` INT NULL DEFAULT '0' AFTER `email`;
");

$installer->endSetup();
