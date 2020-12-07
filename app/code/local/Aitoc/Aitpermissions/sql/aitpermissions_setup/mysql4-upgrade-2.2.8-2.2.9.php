<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

$installer = $this;

$installer->startSetup();

$installer->run($sql = '
ALTER TABLE `' . $this->getTable('aitoc_aitpermissions_advancedrole') . '` ADD COLUMN `can_edit_global_attr` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1;
');

$installer->endSetup();
