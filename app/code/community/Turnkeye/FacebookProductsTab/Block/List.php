<?php

class Turnkeye_FacebookProductsTab_Block_List extends Mage_Catalog_Block_Product_Abstract
{


    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;


    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
	if ($this->_productCollection) {
	    return $this->_productCollection;
	}

	return array();
    }


    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }


    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }


    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }


}

?>
