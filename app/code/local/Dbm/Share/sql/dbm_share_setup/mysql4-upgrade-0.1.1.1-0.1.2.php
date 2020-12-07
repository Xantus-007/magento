<?php

$installer = $this;

$configTable = $this->getTable('core_config_data');
$elementsTable = $this->getTable('dbm_share_element');
$catTable = $this->getTable('dbm_share_category');

//Renaming core_config_data fields
$fields = array(
    'dbm_share/dbm_share_splash/dbm_share_splash_en_uk' => 'dbm_share/dbm_share_splash/dbm_share_splash_en_gb',
    'dbm_share/dbm_share_homescreen/dbm_share_homescreen_en_uk' => 'dbm_share/dbm_share_homescreen/dbm_share_homescreen_en_gb',
    'dbm_share/dbm_share_homebuttons/dbm_share_homebuttons02_en_uk' => 'dbm_share/dbm_share_homebuttons/dbm_share_homebuttons02_en_gb',
    'dbm_share/dbm_share_homebuttons/dbm_share_homebuttons01_en_uk' => 'dbm_share/dbm_share_homebuttons/dbm_share_homebuttons01_en_gb'
);

foreach($fields as $old => $new)
{
    $query = 'UPDATE '.$configTable.' SET path="'.$new.'" WHERE path="'.$old.'"';
    $installer->run($query);
}

//Renaming dbm_share_element fields
//Renaming dbm_share_category fields
$queries = array(
    'ALTER TABLE `'.$elementsTable.'` CHANGE `description_en_uk` `description_en_gb` TEXT NULL',
    'ALTER TABLE `'.$elementsTable.'` CHANGE `ingredients_content_en_uk` `ingredients_content_en_gb` TEXT NULL',
    'ALTER TABLE `'.$elementsTable.'` CHANGE `ingredients_legend_en_uk` `ingredients_legend_en_gb` VARCHAR(255) NULL DEFAULT NULL',
    'ALTER TABLE `'.$elementsTable.'` CHANGE `title_en_uk` `title_en_gb` VARCHAR(45) NULL DEFAULT NULL',
    'ALTER TABLE `'.$catTable.'` CHANGE `title_en_uk` `title_en_gb` VARCHAR(255) NOT NULL DEFAULT ""'
);

foreach($queries as $query)
{
    $installer->run($query);
}

$this->endSetup();