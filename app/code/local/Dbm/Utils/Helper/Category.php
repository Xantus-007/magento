<?php

class Dbm_Utils_Helper_Category extends Mage_Core_Helper_Abstract
{
    public function getRootCategoryForCurrentStore($store = 1)
    {
        $layer = Mage::getSingleton('catalog/layer');
        $layer->setCurrentStore($store);
        $categoryId = $layer->getCurrentStore()->getRootCategoryId();
        return Mage::getModel('catalog/category')->load($categoryId);
    }

    public function getRootCategoryForStore($storeId)
    {
        $store = Mage::getModel('core/store')->load($storeId);
        $category = Mage::getModel('catalog/category');
        $result = null;

        if($store->getId() > 0)
        {
            $category->load($store->getRootCategoryId());
            $result = $category;
        }

        return $result;
    }

    public function getCurrentLayeredCategory()
    {

    }

    public function createCategoryPath(array $namePath, Mage_Catalog_Model_Category $rootCategory = null, $useIds = false)
    {
        if(!$rootCategory)
        {
            $rootCategory = $this->getRootCategoryForCurrentStore();
        }

        $currentCat = $rootCategory;

        foreach($namePath as $idImport => $treeElement)
        {
            $childFound = false;

            //Searching for the children cats to see if name exists
            foreach($currentCat->getChildrenCategories() as $child)
            {
                if($child->getName() == $treeElement)
                {
                    $currentCat = $child;
                    $childFound = true;
                }
            }

            //Create missing category
            if(!$childFound)
            {
                $newCat = Mage::getModel('catalog/category');
                $newCat->setName($treeElement);
                $newCat->setParentId($currentCat->getId());
                $newCat->setIsActive(1);
                $newCat->setIncludeInMenu(1);
                $newCat->setStoreId($rootCategory->getStoreId());
                $newCat->setPath($currentCat->getPath());

                if($useIds && strlen($idImport) > 0 )
                {
                    $newCat->setIdImport($idImport);
                }
                
                $newCat->save();

                $currentCat = $newCat;
            }
        }

        return $currentCat;
    }

    /**
     * Return unique url for product.
     * @param Mage_Catalog_Model_Product $product
     * @param array $restrictedPath
     * @return string
     */
    public function getUnifiedProductUrl(Mage_Catalog_Model_Product $product, $restrictedPath)
    {
        //Searching for categories :
        $catIds = $product->getCategoryIds();
        $category = Mage::getModel('catalog/category');
        $currentCat = 0;

        foreach($catIds as $cat)
        {
            $path = explode('/', Mage::getModel('catalog/category')->load($cat)->getPath());
            $pathCount = count($path);
            $diffCount = count(array_diff($path, $restrictedPath));

            $isPathRestricted = false;

            if($diffCount != $pathCount)
            {
                $isPathRestricted = true;
            }

            if(!$isPathRestricted)
            {
                $currentCat = $cat;
                break;
            }
        }

        $category->load($currentCat);
        $oldCat = Mage::registry('current_category');

        Mage::unregister('current_category');
        Mage::register('current_category', $category);

        $url = $product->getProductUrl();

        Mage::unregister('current_category');
        Mage::register('current_category', $oldCat);

        return $url;
    }
}