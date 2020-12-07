<?php
class Turnkeye_FacebookProductsTab_Block_View extends Mage_Catalog_Block_Navigation 
{
	protected $_products_block;

	public function getProductListHtml($category)
	{
	$block = 'facebookproductstab_products';
	if (!$this->_products_block) {
		$this->_products_block = $this->getLayout()->getBlock($block);
	}

		$collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('name')
			->addAttributeToSelect('sku')
			->addAttributeToSelect('price')
			->addAttributeToSelect('status')
			->addAttributeToSelect('short_description')
			->addAttributeToSelect('small_image')
			->setStoreId($this->getStoreId())
			->addMinimalPrice()
			->addFinalPrice()
			->addTaxPercents()
			->addStoreFilter()
			->addAttributeToFilter('is_facebook', 1)
			->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
		$collection->load();

		Mage::getModel('review/review')->appendSummary($collection);
		$this->_products_block->setCollection($collection);

		return $this->_products_block->toHtml();
	}
}
?>
