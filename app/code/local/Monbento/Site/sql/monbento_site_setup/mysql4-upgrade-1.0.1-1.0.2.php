<?php

$mediaTable = $this->getTable('catalog_product_entity_media_gallery_value');

$setup = $this->startSetup();
$query = 'ALTER TABLE `'.$mediaTable.'` ADD `gallery5` INT NULL AFTER `gallery4`;';
$setup->run($query);

$setup->endSetup();