<?php

$installer = $this->startSetup();

$customerTable = $this->getTable('customer_entity');
$elementAbuseTable = $this->getTable('dbm_share_abuse_element');
$elementTable = $this->getTable('dbm_share_element');
$commentAbuseTable = $this->getTable('dbm_share_abuse_comment');
$commentTable = $this->getTable('dbm_share_comment');

$queries = array();

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$elementAbuseTable.'` (
  `id_customer` INT(10) UNSIGNED NOT NULL ,
  `id_element` INT(10) UNSIGNED NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id_customer`, `id_element`) ,
  INDEX `FK_ABUSE_ELEMENT_customer_id_idx` (`id_customer` ASC) ,
  INDEX `FK_ABUSE_ELEMENT_element_id_idx` (`id_element` ASC) ,
  CONSTRAINT `FK_ABUSE_ELEMENT_CUSTOMER`
    FOREIGN KEY (`id_customer`)
    REFERENCES `' . $customerTable . '` (`entity_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_ABUSE_ELEMENT`
    FOREIGN KEY (`id_element`)
    REFERENCES `' . $elementTable . '` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB';

$queries[] = 'CREATE TABLE IF NOT EXISTS `'.$commentAbuseTable.'` (
  `id_customer` INT(10) UNSIGNED NOT NULL ,
  `id_comment` INT(10) UNSIGNED NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id_customer`, id_comment) ,
  INDEX `FK_ABUSE_COMMENT_customer_idx` (`id_customer` ASC) ,
  INDEX `FK_ABUSE_COMMENT_element_idx` (`id_comment` ASC) ,
  CONSTRAINT `FK_ABUSE_COMMENT_CUSTOMER`
    FOREIGN KEY (`id_customer`)
    REFERENCES `' . $customerTable. '` (`entity_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_ABUSE_COMMENT`
    FOREIGN KEY (`id_comment`)
    REFERENCES `' . $commentTable . '` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB';

foreach($queries as $query)
{
    $installer->run($query);
}

$installer->endSetup();