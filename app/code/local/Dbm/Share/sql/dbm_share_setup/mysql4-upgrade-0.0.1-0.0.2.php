<?php

$elementTable = $this->getTable('dbm_share_element');

$setup = $this->startSetup();
$queries = array();
$locales = array(
    'fr_fr',
    'en_uk',
    'jp_jp',
    'es_es',
    'pt_pt'
);

$queries[] = 'ALTER TABLE `'.$elementTable.'` ADD `cooking_duration` INT NULL AFTER `description_pt_pt`;';
$queries[] = 'ALTER TABLE `'.$elementTable.'` ADD `cooking_duration_unit` VARCHAR(45) NULL AFTER `description_pt_pt`;';
$queries[] = 'ALTER TABLE `'.$elementTable.'` ADD `duration` INT NULL AFTER `description_pt_pt`;';
$queries[] = 'ALTER TABLE `'.$elementTable.'` ADD `duration_unit` VARCHAR(45) NULL AFTER `description_pt_pt`;';

foreach($locales as $locale)
{
    $queries[] = 'ALTER TABLE `'.$elementTable.'` ADD `ingredients_legend_'.$locale.'` VARCHAR(255) NULL AFTER `description_pt_pt`;';
    $queries[] = 'ALTER TABLE `'.$elementTable.'` ADD `ingredients_content_'.$locale.'` TEXT NULL AFTER `description_pt_pt`;';
}

foreach($queries as $query)
{
    $setup->run($query);
}

$setup->endSetup();
