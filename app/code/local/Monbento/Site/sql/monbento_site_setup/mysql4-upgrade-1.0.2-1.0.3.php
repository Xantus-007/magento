<?php

$mediaTable = $this->getTable('catalog_product_entity_media_gallery_value');

$setup = $this->startSetup();
$query = 'ALTER TABLE `'.$mediaTable.'` ADD `gallery6` INT NULL AFTER `gallery5`;';
$setup->run($query);

$setup->endSetup();