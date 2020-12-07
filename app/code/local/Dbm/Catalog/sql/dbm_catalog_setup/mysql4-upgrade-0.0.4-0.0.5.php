<?php
$installer = $this;

$installer->startSetup();

$setup = new Mage_Core_Model_Config();

$setup->saveConfig('tax/calculation/cross_border_trade_enabled', '1', 'stores', 3);

$installer->endSetup();