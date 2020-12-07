<?php

$installer = $this->startSetup();

$followTable = $this->getTable('dbm_share_follow');

$query = 'DROP TABLE `'.$followTable.'`';

$installer->run($query);

$connexion = Mage::getSingleton('core/resource')->getConnection('core_write');
$connexion->resetDdlCache();

$installer->endSetup();