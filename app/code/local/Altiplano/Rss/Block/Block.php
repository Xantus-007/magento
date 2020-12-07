<?php

class Altiplano_Rss_Block_Block extends Mage_Core_Block_Template
{

	protected function _construct() {
	  parent::_construct();
    $this->addData(array(
      'cache_lifetime'    => 3600,
      'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
      'cache_key'					=> $this->getCacheKey()
    ));

    $this->setTemplate('altiplano/rss/block.phtml');
	}

	public function getCacheKey() {
		return "ALTIPLANO_RSS_".md5(serialize($this->getData()));
	}

  protected function _beforeToHtml() {
  	$feed = Zend_Feed_Reader::import($this->getFeedUrl());

		$items = new Varien_Data_Collection();

		$i = 0;
		while($i < $this->getNb() && $feed->current()) {
			$items->addItem($feed->current());
			$feed->next();
			$i++;
		}

    $this->setItemsCollection($items);

    return parent::_beforeToHtml();
	}

	public function _toHtml() {
		return parent::_toHtml();
		$this->unsetData(null);
	}

}