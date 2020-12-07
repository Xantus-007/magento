<?php 

$setup = $this->startSetup();

$queries = array();
$catTable = $this->getTable('dbm_share_category');

$queries[] = 'ALTER TABLE `'.$catTable.'` ADD `meta_description_fr_fr` TEXT NULL AFTER `title_pt_pt`';
$queries[] = 'ALTER TABLE `'.$catTable.'` ADD `meta_description_en_gb` TEXT NULL AFTER `meta_description_fr_fr`';
$queries[] = 'ALTER TABLE `'.$catTable.'` ADD `meta_description_es_es` TEXT NULL AFTER `meta_description_en_gb`';

foreach($queries as $query)
{
    $setup->run($query);
}

$setup->endSetup();