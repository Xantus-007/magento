<?php

// Altiplano Links
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE `cms_page` ADD `libelle` varchar(255) default NULL, ADD `position` int(11) default NULL, ADD `parent` smallint(6) default NULL;");
$installer->endSetup();