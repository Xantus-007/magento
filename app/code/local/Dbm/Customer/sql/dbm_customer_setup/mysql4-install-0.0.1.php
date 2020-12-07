<?php

$installer = $this;

$linkTable = $this->getTable('dbm_customer_link');
$customerTable = $this->getTable('customer_entity');

$queries = array();

$queries[] =  'CREATE  TABLE IF NOT EXISTS `'.$linkTable.'` (
  `id_customer` INT(10) UNSIGNED NOT NULL ,
  `id_following` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_customer`, `id_following`) ,
  INDEX `FK_FOLLOW_CUSTOMER_idx` (`id_customer` ASC) ,
  INDEX `FK_FOLLOW_CUSTOMER_DESTINATION_idx` (`id_following` ASC) ,
  CONSTRAINT `FK_CUSTOMER_LINK_SOURCE`
    FOREIGN KEY (`id_customer` )
    REFERENCES `'.$customerTable.'` (`entity_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_LINK_DESTINATION`
    FOREIGN KEY (`id_following` )
    REFERENCES `'.$customerTable.'` (`entity_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB';

foreach($queries as $query)
{
    $installer->run($query);
}

$installer->endSetup();