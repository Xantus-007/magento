<?php
class Monbento_Catalog_Block_Product_Featured extends Mage_Catalog_Block_Product_Abstract
{
  public function getFeaturedProducts() {
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('catalog_read');
		$productEntityIntTable = (string)Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_int';
		$eavAttributeTable = $resource->getTableName('eav/attribute');
		$categoryProductTable = $resource->getTableName('catalog/category_product');

		$select = $read->select()
			->distinct(true)
			->from(array('cp'=>$categoryProductTable), 'product_id')
			->join(array('pei'=>$productEntityIntTable), 'pei.entity_id=cp.product_id', array())
			->joinNatural(array('ea'=>$eavAttributeTable))
			->where('pei.value=1')
			->where('ea.attribute_code="featured"');
			
			
			/*SELECT DISTINCT `cp`.`product_id`, `cpi`.`visibility`, `ea`.* FROM `catalog_category_product` AS `cp` JOIN `catalog_category_product_index` AS `cpi` ON cpi.product_id=cp.product_id
 INNER JOIN `catalog_product_entity_int` AS `pei` ON pei.entity_id=cp.product_id
 NATURAL JOIN `eav_attribute` AS `ea` WHERE (pei.value=1) AND (ea.attribute_code="featured")*/

		$res = $read->fetchAll($select);
		return $res;
	}
}
?>