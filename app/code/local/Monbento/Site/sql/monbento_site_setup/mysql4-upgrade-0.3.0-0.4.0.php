<?php

$mediaTable = $this->getTable('catalog_product_entity_media_gallery_value');

$setup = $this->startSetup();
$query = 'ALTER TABLE `'.$mediaTable.'` ADD `gallery1` INT NULL AFTER `disabled`;';
$setup->run($query);

$query = 'ALTER TABLE `'.$mediaTable.'` ADD `gallery2` INT NULL AFTER `gallery1`;';
$setup->run($query);

$setup->endSetup();