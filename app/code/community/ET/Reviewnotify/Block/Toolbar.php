<?php
/**
 * ET Web Solutions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_Reviewnotify
 * @copyright  Copyright (c) 2011 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class ET_Reviewnotify_Block_Toolbar extends Mage_Adminhtml_Block_Template
{
	/**
	 * Get pending reviews as a collection, might be none.
	 *
	 * @return Mage_Review_Model_Mysql4_Review_Product_Collection
	 */
	public function getPending()
	{
		/* @var $model Mage_Review_Model_Review */
		$model = Mage::getModel('review/review');
		/* @var $collection Mage_Review_Model_Mysql4_Review_Product_Collection */
		$collection = $model->getProductCollection()
			->addStatusFilter($model->getPendingStatus())
			->load();
		return $collection;
	}

	/**
	 * Get edit URL for a product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	public function getEditUrl($product)
	{
		$params = array('id' => $product->getId());
		return $this->getUrl('adminhtml/catalog_product/edit', $params);
	}

	/**
	 * Get edit URL for a product's review
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	public function getReviewUrl($product)
	{
		$params = array('id'=>$product->getReviewId());
		return $this->getUrl('*/catalog_product_review/edit', $params);
	}

}
