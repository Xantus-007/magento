<?php

$mediaTable = $this->getTable('catalog_product_entity_media_gallery_value');

$setup = $this->startSetup();
$query = 'ALTER TABLE `'.$mediaTable.'` ADD `gallery3` INT NULL AFTER `gallery2`;';
$setup->run($query);

$query = 'ALTER TABLE `'.$mediaTable.'` ADD `gallery4` INT NULL AFTER `gallery3`;';
$setup->run($query);

$setup->endSetup();