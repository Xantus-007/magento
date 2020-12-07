<?php

$blogTable = $this->getTable('aw_blog');

$setup = $this->startSetup();
$query = 'ALTER TABLE `'.$blogTable.'` ADD `show_in_home` INT NULL AFTER `status`;';
$setup->run($query);

$query = 'ALTER TABLE `'.$blogTable.'` ADD `post_image` VARCHAR(255) NULL AFTER `show_in_home`;';
$setup->run($query);

$setup->endSetup();