<?php

class Dbm_Utils_Helper_Product extends Mage_Core_Helper_Abstract
{
    /**
     * Returns available values for given attribute.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param type $attributeName
     * @param type $split
     * @return type
     */
    public function getAttributeValues(Mage_Catalog_Model_Product $product, $attributeName, $split = null)
    {
        $attributes = $product->getAttributes();
        $attributeValue = null;
        $result = null;

        if(array_key_exists($attributeName , $attributes)){
            $attributesobj = $attributes["{$attributeName}"];
            $result = $attributesobj->getFrontend()->getValue($product);
        }

        if($split && $result)
        {
            $result = explode($split, $result);
        }

        return $result;
    }

    /**
     * Get an attribute value name for a given product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param type $attributeName
     * @return type
     */
    public function getAttributeValueForProduct(Mage_Catalog_Model_Product &$product, $attributeCode)
    {
        return $product->getResource()->getAttribute($attributeCode)->getFrontEnd()->getValue($product);
    }

    /**
     * Returns attribute type list.
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection
     */
    public function getAttributeSetList()
    {
        $entityType = Mage::getModel('catalog/product')->getResource()->getEntityType();
        $result = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityType->getId());
        /*
        $result = array();
        foreach ($collection as $attributeSet) {
            $result[] = array(
                'set_id' => $attributeSet->getId(),
                'name'   => $attributeSet->getAttributeSetName()
            );

        }
        */

        return $result;
    }

    /**
     * Search attribute set from name.
     *
     * @param string $name
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    public function getAttributeSetByName($name)
    {
        $result = null;
        $list = $this->getAttributeSetList();

        foreach($list as $attributeSet)
        {
            if($attributeSet->getAttributeSetName() == $name)
            {
                $result = $attributeSet;
            }
        }

        return $result;
    }

    /**
     * Returns attribute set from id.
     * @param int $idAttributeSet
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    public function getAttributeSetById($idAttributeSet)
    {
        return Mage::getModel('eav/entity_attribute_set')->load($idAttributeSet);
    }

    /**
     * Updates quantity for a new or existing product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param boolean $isInStock
     */
    public function updateQuantityForProduct(Mage_Catalog_Model_Product $product, $qty, $isInStock = null, $useConfigManageStock = null)
    {
        if(!$isInStock)
        {
            $isInStock = $qty > 0;
        }

        if(!$useConfigManageStock)
        {
            $useConfigManageStock = 0;
        }

        $stockItem = Mage::getModel('cataloginventory/stock_item');
        $stockItem->loadByProduct($product->getId());

        if (! $stockItem->getId()) {
            $stockItem->setProductId($product->getId())->setStockId(1);
            $stockItem->save();
            $stockItem->loadByProduct($product->getId());
        }

        //$stockItem->setIsInStock($isInStock);
        $stockItem->setUseConfigManageStock($useConfigManageStock);
        $stockItem->setManageStock(1);
        $stockItem->setIsInStock($isInStock)->setStockStatusChangedAutomaticallyFlag(true);
        $stockItem->setQty($qty);
        $stockItem->save();
    }

    public function prepareFrontProductCollection(&$collection, $visibility = true)
    {
        $collection->addAttributeToFilter('status', 1);
        if($visibility)
        {
            $collection->addAttributeToFilter('visibility', 4);
        }

        $collection->addAttributeToSelect(array(
            'status',
            'name',
            'price',
            'special_price',
            'short_description',
            'image',
            'small_image',
            'thumbnail'
        ),'inner');
    }

    public function getAttributeByCode($attributeCode)
    {
        return Mage::getSingleton('eav/config')
            ->getAttribute('catalog_product', $attributeCode);
    }

    public function getAttributeValuesByAttributeCode($attributeCode)
    {
        //TODO: factorize this.
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->addFieldToFilter('attribute_code', $attributeCode)
            ->load(false);
        $attribute = $attributes->getFirstItem();
        $attribute->setSourceModel('eav/entity_attribute_source_table');
        $atts = $attribute->getSource()->getAllOptions(false);
        $result = array();
        foreach($atts as $tmp)
            $result[$tmp['label']] = $tmp['value'];
        return $result;
    }
    
    public function getAttributeOptionIdByLabel($attribute_code, $label)
    {
        $attribute_model = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
        $attribute_code = $attribute_model->getIdByCode('catalog_product', $attribute_code);
        $attribute = $attribute_model->load($attribute_code);
    
        $attribute_table = $attribute_options_model->setAttribute($attribute);
        $options = $attribute_options_model->getAllOptions(false);
    
        foreach($options as $option)
        {
            if ($option['label'] == $label)
            {
                $optionId = $option['value'];
                break;
            }
        }
    
        return $optionId;
    }

    /**
     * Get attributes used for create configurable given product.
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getConfigurableAttributesByProduct($product)
    {
        $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        $attributeOptions = array();
        foreach ($productAttributeOptions as $productAttribute) 
        {
            $attributeOptions[$productAttribute['attribute_code']]['label'] = $productAttribute['label'];
            foreach ($productAttribute['values'] as $attribute)
            {
                $attributeOptions[$productAttribute['attribute_code']]['options'][$attribute['value_index']] = $attribute['store_label'];
            }
        }
        
        return $attributeOptions;
    }

    public function addOptionToAttribute($attId, $opt, $value, $order = null)
    {
        if(strlen($value) > 0)
        {
            $optId = base64_encode($value);
            //TODO: factorize this.
            $option['attribute_id'] = $attId;
            $option['value'][$optId][0] = $value;

            if(!is_null($order))
            {
                $option['order'][$optId] = intval($order);
            }

            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
            $setup->addAttributeOption($option);
        }
    }

    /**
     * Get declinaisons qty sum for a given product.
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getDeclinaisonsQtySum(Mage_Catalog_Model_Product $product)
    {
        $declinaisons = $this->getDeclinaisons($product);
        $result = 0;

        foreach($declinaisons as $declinaison)
        {
            $result += $this->getProductQty($declinaison);//Mage::getModel('cataloginventory/stock_item')->loadByProduct($declinaison)->getQty();
        }

        return $result;
    }

    /**
     * Returns declinaisons attribute.
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeName
     * @return int
     */
    public function getDeclinaisonsAttributes(Mage_Catalog_Model_Product $product, $attributeName)
    {
        $declinaisons = $this->getDeclinaisons($product);
        $result = array();

        foreach($declinaisons as $declinaison)
        {
            $qty = $this->getProductQty($declinaison);

            if($qty > 0)
            {
                $tmpValue = $this->getAttributeValueForProduct($declinaison, $attributeName);
                if($tmpValue)
                {
                    $result[] = $tmpValue;
                }
            }
        }

        return $result;
    }

    /**
     * Returns declinaisons for a given parent product.
     * @param Mage_Catalog_Model_Product $product
     * @param boolean $filterSaleable
     * @return array
     */
    public function getDeclinaisons(Mage_Catalog_Model_Product $product, $filterSaleable = true)
    {
        $children = Mage::getModel('catalog/product_type_configurable')
                ->getUsedProducts(null, $product);

        $remove = array();
        foreach($children as $i => $declinaison)
        {
            if(!$declinaison->isSaleable() && $filterSaleable)
            {
                $remove[] = $i;
            }
        }

        foreach($remove as $key)
        {
            unset($children[$key]);
        }

        return $children;
    }

    /**
     * Returns product QTY.
     * @param MagE_Catalog_Model_Product $product
     * @return float
     */
    public function getProductQty($product)
    {
        return Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
    }

    /**
     * Returns parent product ids for a given product.
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getParentProductIds(Mage_Catalog_Model_Product $product)
    {
        return Mage::getModel( 'catalog/product_type_configurable' )->getParentIdsByChild($product->getId());
    }

    /**
     * Returns first parent of current product.
     * @param Mage_Catalog_Model_Product $product
     * @return mixed
     */
    public function getFirstParent(Mage_Catalog_Model_Product $product)
    {
        $result = null;
        $model = Mage::getModel('catalog/product');
        $id = current($this->getParentProductIds($product));

        if($id > 0 && $model->load($id))
        {
            $result = $model;
        }

        return $result;
    }

    /**
     * Returns parented product collection excluding produts by attribute set.
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @param boolean $restrictAttributeSets
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getParentedCollection($collection, $restrictAttributeSets = array())
    {
        $productIds = array();

        foreach($collection as $product)
        {
            $isSimple = in_array($product->getAttributeSetId(), $restrictAttributeSets);

            if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE || $isSimple)
            {
                $productIds[] = $product->getId();
            }
            else
            {
                $ids = Mage::getResourceSingleton('catalog/product_type_configurable')
                  ->getParentIdsByChild($product->getId());

                if(is_array($ids))
                {
                    foreach($ids as $id)
                    {
                        $productIds[] = $id;
                    }
                }
            }
        }

        $finalCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('entity_id', array('in'=> $productIds))
        ;

        return $finalCollection;
    }

    public function addProductQtyToCollection(&$collection)
    {
        $collection->addAttributeToSelect('qty.*')
            ->addAttributeToSelect('qty.*')
            ->joinField(
                'qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            )->joinField(
                'is_in_stock',
                'cataloginventory/stock_item',
                'is_in_stock',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );
    }

    public function getMinprice(Mage_Catalog_Model_Product $product, $useContainer = false)
    {
        $result = array(
            'price' => '',
            'formatted_price' => '',
            'special_price' => '',
            'formatted_special_price' => '',
            'has_special_price' => false

        );
        $coreHelper = Mage::helper('core');

        switch($product->getTypeId())
        {
            case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
                $result['price'] = $product->getPrice();
                $result['formatted_price'] = $coreHelper->currency($product->getPrice(), true, $useContainer);
                $result['special_price'] = $product->getFinalPrice();
                $result['formatted_special_price'] = $coreHelper->currency($product->getFinalPrice(), true, $useContainer);
                $result['has_special_price'] = $result['price'] != $result['special_price'];

                break;
            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                $result = $this->getMinPriceForConfigurableProduct($product)->getData();

                break;
        }

        return new Varien_Object($result);
    }

    public function getMinPriceForConfigurableProduct(Mage_Catalog_Model_Product $product)
    {
        $declinaisons = $this->getDeclinaisons($product);
        $prices = array();
        $result = array();

        foreach($declinaisons as $decli)
        {
            $tmpPrice = $this->getMinprice($decli);
            if($tmpPrice->getHasSpecialPrice())
            {
                $minPrice = $tmpPrice->getPrice();
            }
            else
            {
                $minPrice = $tmpPrice->getSpecialPrice();
            }

            $prices[$minPrice] = $tmpPrice;
        }

        sort($prices);
        reset($prices);

        return current($prices);
    }

    /**
     * Return parent product from given product.
     * Return current product if no parents found.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return \Mage_Catalog_Model_Product
     */
    public function getFinalParentProduct(Mage_Catalog_Model_Product $product)
    {
        $result = $product;
        if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
        {
            $result = $this->getFirstParent($product);
            if(!($result && $result->getId() > 0))
            {
                $result = $product;
            }
        }

        return $result;
    }

    public function updateStockStatusForConfProduct(Mage_Catalog_Model_Product $product)
    {
        if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
        {
            //Update inventory
            $decliQty = $this->getDeclinaisonsQtySum($product);

            $data = $product->getStockItem()->getData();
            $data['is_in_stock'] = intval($decliQty > 0);
            $product->setData('stock_data',$data);
            $product->save();
        }
    }

    public function fastQtyUpdate(Mage_Catalog_Model_Product $product, $qty)
    {
        $coreResource = Mage::getSingleton('core/resource') ;
    	// fetch write database connection that is used in Mage_Core module
    	$write = $coreResource->getConnection('core_write');

        return $write->query('UPDATE cataloginventory_stock_item s_i, cataloginventory_stock_status s_s
            SET     s_i.qty = "'.$qty.'", s_i.is_in_stock = IF("' . $qty . '">0, 1,0),
            s_s.qty = "' . $qty . '", s_s.stock_status = IF("' . $qty . '">0, 1,0)
            WHERE s_i.product_id = "' . $product->getId().'" AND s_i.product_id = s_s.product_id ');
    }
}