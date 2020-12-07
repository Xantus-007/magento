<?php

class Dbm_Catalog_Model_Api_V2 extends Mage_Api_Model_Resource_Abstract
{
    const OPTION_COUVERCLE_ID = 6;

    public function getBundledProducts($storeView, $productId)
    {
        $defaultValues = array(
            7 => 3547, //Caissette
            6 => 3384, //Couvercle
            5 => 3397, //Récipient haut
            4 => 3397, //Recipient bas
            3 => 3563, //Intermediaire haut
            2 => 3563, //Intermediaire bas
            1 => 3565 //Elastique
        );
        
        $result = array();
        $parent = Mage::getModel('catalog/product')->load($productId);
        Mage::app()->setCurrentStore($this->_getStoreId($storeView));
        
        $priceModel = Mage::getModel('bundle/product_price');
        
        $result['product_id'] = $parent->getId();
        $result['min_price'] = $priceModel->getMinimalPrice($parent);
        $result['children'] = array();

        $result['parent_entity'] = Mage::helper('dbm_catalog/api')->prepareProductData($parent);
        
        if($parent->getId() > 0)
        {
            $optionsCollection = $parent->getTypeInstance(true)->getOptionsCollection($parent);

            $selectionCollection = $parent->getTypeInstance(true)->getSelectionsCollection(
                $parent->getTypeInstance(true)->getOptionsIds($parent),
                $parent
            );
            
            $optionsCollection->appendSelections($selectionCollection);
            
            foreach($optionsCollection as $option)
            {
                $tmpResult = array(
                    'option_id' => $option->getOptionId(),
                    'parent_id' => $option->getParentId(),
                    'required' => $option->getRequired(),
                    'position' => $option->getPosition(),
                    'type' => $option->getType(),
                    'default_title' => $option->getDefaultTitle(),
                    'has_zoom' => $option->getOptionId() == 6,
                    'selection' => array()
                );
                
                $hasDefault = false;
                
                foreach($option->getSelections() as $child)
                {
                    $tmpSelection = Mage::helper('dbm_catalog/api')->prepareProductData($child, true, null, true);
                    $tmpResult['selection'][] = $tmpSelection;
                    
                    if($tmpSelection['is_default'])
                    {
                        $hasDefault = true;
                    }
                }
                
                if(!$hasDefault)
                {
                    $child = Mage::getModel('catalog/product')->load($defaultValues[$option->getOptionId()]);
                    $tmpSelection = Mage::helper('dbm_catalog/api')->prepareProductData($child, true, null, true);
                    $tmpSelection['is_default'] = true;
                    
                    
                    //Parsing options :
                    $selections = $parent->getTypeInstance()->getSelectionsCollection(array($option->getOptionId()), $parent);
                    
                    foreach($selections as $test)
                    {
                        foreach($selections as $selection)
                        {
                            if($selection->getProductId() == $child->getId())
                            {
                                $tmpSelection['selection_id'] = $selection->getSelectionId();
                                $tmpSelection['option_id'] = $selection->getOptionId();
                            }
                        }
                    }
                    
                    array_unshift($tmpResult['selection'], $tmpSelection);
                }
                
                $result['children'][] = $tmpResult;
            }
        }
        return $result;
    }

    public function makeBundleImage($storeView, $idProduct, $optionsIds)
    {
        $optionsIds = Mage::helper('dbm_share')->unWsdlize($optionsIds);

        Mage::app()->setCurrentStore($this->_getStoreId($storeView));
        $product = Mage::getModel('catalog/product')->load($idProduct);
        $sizes = Mage::helper('dbm_catalog/image')->getSizes();

        if($product->getId())
        {
            $bundleOptions = Mage::getModel('bundle/product_type')->getOptionsByIds(array_flip($optionsIds), $product);
            $optionType = array();
            $bundleSelections = Mage::getModel('bundle/product_type')->getSelectionsByIds($optionsIds, $product);
            
            foreach ($bundleOptions as $key => $bundleOption) {
                $optionTitle = explode('-',$bundleOption->getTitle());
                $optionType[$optionsIds[$bundleOption->getid()]] = $optionTitle[0];
            }
            
            $optionsArray = array();
            foreach ($bundleSelections as $key=>$bundleSelection) {
                $optionsArray[$optionType[$key]] = $bundleSelection->getEntityId() ;
            }

            $result = Mage::getModel('bundle/product_type')->getCustomImage($optionsArray, $sizes['bundle_thumbnail'][0], $sizes['bundle_thumbnail'][1]);
        }
        else
        {
            Mage::throwException('Product not found');
        }

        return $result;
    }

    protected function _getStoreId($store = null)
    {
        $trans = Mage::helper('dbm_share');
        if (is_null($store)) {
            $store = ($this->_getSession()->hasData($this->_storeIdSessionField)
                        ? $this->_getSession()->getData($this->_storeIdSessionField) : 0);
        }

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->_fault($trans->__('Store does not exist'));
        }

        return $storeId;
    }
}
