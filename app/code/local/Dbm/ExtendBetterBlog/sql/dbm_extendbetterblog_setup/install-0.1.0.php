<?php

$this->startSetup();

$table = $this->getTable('eav/attribute');
$entityTypeId = Mage::getModel('mageplaza_betterblog/post')->getResource()->getTypeId();
$query = 'UPDATE '.$table.' SET is_user_defined = 1 WHERE entity_type_id = '.$entityTypeId.' AND attribute_code IN ("in_rss","allow_comment");';
$this->run($query);

$this->endSetup();
