<?php 

$installer = $this;

$photoTable = $this->getTable('dbm_share_photo');

$query = 'ALTER TABLE `'.$photoTable.'` ADD `gmaps_label` TEXT NULL  AFTER `lng`;';
$installer->run($query);

$installer->endSetup();