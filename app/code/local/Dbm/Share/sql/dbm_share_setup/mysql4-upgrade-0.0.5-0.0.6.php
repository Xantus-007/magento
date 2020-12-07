<?php

$installer = $this;
$queries = array();

//Correct categories
$categoryTable = $this->getTable('dbm_share_category');
$queries[] = 'ALTER TABLE `'.$categoryTable.'` CHANGE `title_jp_jp` `title_ja_jp` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT \'\';';

//Correct elements
$elementsTable = $this->getTable('dbm_share_element');
$queries[] = 'ALTER TABLE `'.$elementsTable.'` CHANGE `title_jp_jp` `title_ja_jp` VARCHAR(45)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT NULL;';
$queries[] = 'ALTER TABLE `'.$elementsTable.'` CHANGE `description_jp_jp` `description_ja_jp` TEXT  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL;';
$queries[] = 'ALTER TABLE `'.$elementsTable.'` CHANGE `ingredients_content_jp_jp` `ingredients_content_ja_jp` TEXT  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL;';
$queries[] = 'ALTER TABLE `'.$elementsTable.'` CHANGE `ingredients_legend_jp_jp` `ingredients_legend_ja_jp` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT NULL;';

foreach($queries as $query)
{
    $installer->run($query);
}

$installer->endSetup();