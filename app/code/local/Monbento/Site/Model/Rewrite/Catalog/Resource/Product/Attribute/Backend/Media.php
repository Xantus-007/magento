<?php

class Monbento_Site_Model_Rewrite_Catalog_Resource_Product_Attribute_Backend_Media extends Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media
{
    /**
     * Get select to retrieve media gallery images
     * for given product IDs.
     *
     * @param array $productIds
     * @param $storeId
     * @param int $attributeId
     * @return Varien_Db_Select
     */
    protected function _getLoadGallerySelect(array $productIds, $storeId, $attributeId) {
        $adapter = $this->_getReadAdapter();

        $positionCheckSql = $adapter->getCheckSql('value.position IS NULL', 'default_value.position', 'value.position');

        // Select gallery images for product
        $select = $adapter->select()
            ->from(
                array('main'=>$this->getMainTable()),
                array('value_id', 'value AS file', 'product_id' => 'entity_id')
            )
            ->joinLeft(
                array('value' => $this->getTable(self::GALLERY_VALUE_TABLE)),
                $adapter->quoteInto('main.value_id = value.value_id AND value.store_id = ?', (int)$storeId),
                array('label','position','disabled', 'gallery1', 'gallery2', 'gallery3', 'gallery4', 'gallery5', 'gallery6')
            )
            ->joinLeft( // Joining default values
                array('default_value' => $this->getTable(self::GALLERY_VALUE_TABLE)),
                'main.value_id = default_value.value_id AND default_value.store_id = 0',
                array(
                    'label_default' => 'label',
                    'position_default' => 'position',
                    'disabled_default' => 'disabled',
                    'gallery1_default' => 'gallery1',
                    'gallery2_default' => 'gallery2',
                    'gallery3_default' => 'gallery3',
                    'gallery4_default' => 'gallery4',
                    'gallery5_default' => 'gallery5',
                    'gallery6_default' => 'gallery6'
                )
            )
            ->where('main.attribute_id = ?', $attributeId)
            ->where('main.entity_id in (?)', $productIds)
            ->order($positionCheckSql . ' ' . Varien_Db_Select::SQL_ASC);

        return $select;
    }
}
