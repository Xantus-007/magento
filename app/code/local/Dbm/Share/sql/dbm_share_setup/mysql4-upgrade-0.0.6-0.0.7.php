<?php

$installer = $this;

$commentTable = $this->getTable('dbm_share_comment');
$query = 'ALTER TABLE `'.$commentTable.'` CHANGE `message` `message` TEXT  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL;';

$installer->run($query);

$installer->endSetup();