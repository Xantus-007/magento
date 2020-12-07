<?php

$installer = $this;

$installer->startSetup();

$customerTable = $this->getTable('customer_entity');
$elementsTable = $this->getTable('dbm_share_element');
$categoryTable = $this->getTable('dbm_share_category');
$photoTable = $this->getTable('dbm_share_photo');
$commentTable = $this->getTable('dbm_share_comment');
$likeTable = $this->getTable('dbm_share_like');
$categoryLinkTable = $this->getTable('dbm_share_element_category');
$followTable = $this->getTable('dbm_share_follow');

$queries = array();

//Elements table
$queries[] = 'CREATE  TABLE IF NOT EXISTS `'.$elementsTable.'` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_customer` INT(10) UNSIGNED NOT NULL ,
  `type` VARCHAR(45) NULL ,
  `level` INT NULL DEFAULT 0 ,
  `price` INT NULL DEFAULT 0 ,
  `title_fr_fr` VARCHAR(255) NOT NULL ,
  `title_en_uk` VARCHAR(45) NULL ,
  `title_jp_jp` VARCHAR(45) NULL ,
  `title_es_es` VARCHAR(45) NULL ,
  `title_pt_pt` VARCHAR(45) NULL ,
  `description_fr_fr` TEXT NULL ,
  `description_en_uk` TEXT NULL ,
  `description_jp_jp` TEXT NULL ,
  `description_es_es` TEXT NULL ,
  `description_pt_pt` TEXT NULL ,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `entity_id_idx` (`id_customer` ASC) ,
  CONSTRAINT `FK_ELEMENT_CUSTOMER`
    FOREIGN KEY (`id_customer`)
    REFERENCES `'.$customerTable.'` (`entity_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB';

//Category table
$queries[] = 'CREATE  TABLE IF NOT EXISTS `'.$categoryTable.'` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `image` VARCHAR(45) NOT NULL ,
  `title_fr_fr` VARCHAR(255) NOT NULL ,
  `title_en_uk` VARCHAR(255) NOT NULL ,
  `title_jp_jp` VARCHAR(255) NOT NULL ,
  `title_es_es` VARCHAR(255) NOT NULL ,
  `title_pt_pt` VARCHAR(255) NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB';

//Photo table
$queries[] = 'CREATE  TABLE IF NOT EXISTS `'.$photoTable.'` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_element` INT(10) UNSIGNED NOT NULL ,
  `filename` VARCHAR(45) NOT NULL ,
  `md5` VARCHAR(45) NOT NULL ,
  `lat` VARCHAR(255) NULL ,
  `lng` VARCHAR(255) NULL ,
  `created_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `id_element_idx` (`id_element` ASC) ,
  CONSTRAINT `FK_PHOTO_ELEMENT`
    FOREIGN KEY (`id_element` )
    REFERENCES `'.$elementsTable.'` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB';

//Comment table
$queries[] = 'CREATE  TABLE IF NOT EXISTS `'.$commentTable.'` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_customer` INT(10) UNSIGNED NOT NULL ,
  `id_element` INT(10) UNSIGNED NOT NULL ,
  `message` VARCHAR(45) NOT NULL ,
  `status` INT NOT NULL DEFAULT 1 ,
  `created_at` DATETIME NOT NULL ,
  INDEX `entity_id_idx` (`id_customer` ASC) ,
  PRIMARY KEY (`id`) ,
  INDEX `id_idx` (`id_element` ASC) ,
  CONSTRAINT `FK_COMMENT_CUSTOMER`
    FOREIGN KEY (`id_customer` )
    REFERENCES `'.$customerTable.'` (`entity_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_COMMENT_ELEMENT`
    FOREIGN KEY (`id_element` )
    REFERENCES `'.$elementsTable.'` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB';

//Likes table
$queries[] = 'CREATE  TABLE IF NOT EXISTS `'.$likeTable.'` (
  `id_element` INT(10) UNSIGNED NOT NULL ,
  `id_customer` INT(10) UNSIGNED NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id_customer`, `id_element`) ,
  INDEX `entity_id_idx` (`id_customer` ASC) ,
  CONSTRAINT `FK_LIKE_ELEMENT`
    FOREIGN KEY (`id_element` )
    REFERENCES `'.$elementsTable.'` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_LIKE_CUSTOMER`
    FOREIGN KEY (`id_customer` )
    REFERENCES `'.$customerTable.'` (`entity_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB';

//category link
$queries[] = 'CREATE  TABLE IF NOT EXISTS `'.$categoryLinkTable.'` (
  `id_element` INT(10) UNSIGNED NOT NULL ,
  `id_category` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_element`, `id_category`) ,
  INDEX `id_idx` (`id_element` ASC) ,
  INDEX `FK_CATEGORY_idx` (`id_category` ASC) ,
  CONSTRAINT `FK_CATEGORY_LINK_ELEMENT`
    FOREIGN KEY (`id_element` )
    REFERENCES `'.$elementsTable.'` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_LINK_CATEGORY`
    FOREIGN KEY (`id_category` )
    REFERENCES `'.$categoryTable.'` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB';


$queries[] = 'CREATE  TABLE IF NOT EXISTS `'.$followTable.'` (
  `id_customer` INT(10) UNSIGNED NOT NULL ,
  `following` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_customer`, `following`) ,
  INDEX `FK_FOLLOW_CUSTOMER_idx` (`id_customer` ASC) ,
  INDEX `FK_FOLLOW_CUSTOMER_DESTINATION_idx` (`following` ASC) ,
  CONSTRAINT `FK_FOLLOW_CUSTOMER_SOURCE`
    FOREIGN KEY (`id_customer` )
    REFERENCES `'.$customerTable.'` (`entity_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_FOLLOW_CUSTOMER_DESTINATION`
    FOREIGN KEY (`following` )
    REFERENCES `'.$customerTable.'` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB';

/*
$queries[] = 'CREATE  TABLE IF NOT EXISTS `'.$followTable.'` (
  `id_customer` INT(10) UNSIGNED NOT NULL ,
  `following` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_customer`, `following`) ,
  CONSTRAINT `FK_FOLLOW_CUSTOMER`
    FOREIGN KEY (`id_customer` , `following`)
    REFERENCES `'.$customerTable.'` (`entity_id` , `entity_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB';
*/
foreach($queries as $query)
{
    $installer->run($query);
}

$installer->endSetup();