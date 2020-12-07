<?php
$installer = $this;
$installer->startSetup();
$setup = new Mage_Core_Model_Config();
$setup->saveConfig('cookielaw/content/cms_page', 'charte-protection-donnees-personnelles-gestion-cookies', 'default', 0);
$installer->endSetup();
