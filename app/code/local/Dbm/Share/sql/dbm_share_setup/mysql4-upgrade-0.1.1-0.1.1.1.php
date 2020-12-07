<?php

$installer = $this;

$elementsTable = $this->getTable('dbm_share_element');

$query = 'ALTER TABLE '.$elementsTable.' MODIFY title_fr_fr VARCHAR(255) NULL';
$installer->run($query);

$installer->endSetup();