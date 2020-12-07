<?php
/**
 * Monbento_FeaturedProducts extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Monbento
 * @package    Monbento_FeaturedProducts
 * @copyright  Copyright (c) 2010 Anthony Charrex
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Monbento
 * @package    Monbento_FeaturedProducts
 * @author     Anthony Charrex <anthony.charrax@gmail.com>
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$entityTypeId     = $installer->getEntityTypeId('catalog_product');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('catalog_product', 'is_featured', array(
    'type'				=>	'int',
    'input'             =>	'boolean',
    'default'           =>	0,
    'source'            =>	'eav/entity_attribute_source_boolean',
    'global'            =>	Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'label'             =>	'Is featured',
    'required'          =>	false,
    'is_configurable'   =>	true,
));

$installer->addAttributeToGroup(
	$entityTypeId,
	$attributeSetId,
	$attributeGroupId,
	'is_featured',
	255
);

$installer->endSetup();

