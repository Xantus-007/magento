<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('wizkunde_configurablebundle'))
    ->addColumn(
        'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID'
    )
    ->addColumn(
        'name', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'Name'
    );
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('wizkunde_configurablebundle_image'))
    ->addColumn(
        'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID'
    )
    ->addColumn(
        'image_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable'  => false,
        ), 'Image ID'
    )
    ->addColumn(
        'path', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'Path'
    )
    ->addColumn(
        'sort', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable'  => false,
        ), 'Sort Order'
    )
    ->addColumn(
        'main', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'Main Image'
    )
    ->addColumn(
        'thumbnail', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'Thumbnail'
    )
    ->addColumn(
        'small_thumbnail', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'Small Thumbnail'
    );
;

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('wizkunde_configurablebundle_product'))
    ->addColumn(
        'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID'
    )
    ->addColumn(
        'image_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable'  => false,
        ), 'Image ID'
    )
    ->addColumn(
        'product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable'  => false,
        ), 'Product ID'
    );
;

$installer->getConnection()->createTable($table);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'wizkunde_configurablebundle_image',
        'image_id',
        'wizkunde_configurablebundle',
        'id'
    ),
    $installer->getTable('wizkunde_configurablebundle_image'), 'image_id', $installer->getTable('wizkunde_configurablebundle'), 'id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'wizkunde_configurablebundle_product',
        'image_id',
        'wizkunde_configurablebundle',
        'id'
    ),
    $installer->getTable('wizkunde_configurablebundle_product'), 'image_id', $installer->getTable('wizkunde_configurablebundle'), 'id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'wizkunde_configurablebundle_product',
        'product_id',
        'catalog_product_entity',
        'entity_id'
    ),
    $installer->getTable('wizkunde_configurablebundle_product'), 'product_id', 'catalog_product_entity', 'entity_id',
    Varien_Db_Ddl_Table::ACTION_NO_ACTION, Varien_Db_Ddl_Table::ACTION_NO_ACTION
);


$installer->endSetup();