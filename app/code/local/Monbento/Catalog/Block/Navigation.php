<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog navigation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Monbento_Catalog_Block_Navigation extends Mage_Catalog_Block_Navigation
{

    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) {
            $html .= $this->_renderCategoryMenuItemHtml(
                $category,
                $level,
                ($j == $activeCategoriesCount - 1),
                ($j == 0),
                true,
                $outermostItemClass,
                $childrenWrapClass,
                true
            );
            $j++;
        }

        return $html;
    }
    
    public function renderCategoriesNewMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = 'child-newMenu')
    {
        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) {
            $html .= $this->_renderCategoryNewMenuItemHtml(
                $category,
                $level,
                ($j == $activeCategoriesCount - 1),
                ($j == 0),
                true,
                $outermostItemClass,
                $childrenWrapClass,
                true
            );
            $j++;
        }

        return $html;
    }


    /**
     * Render category to html
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @param boolean Whether ot not this item is first, affects list item class
     * @param boolean Whether ot not this item is outermost, affects list item class
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @param boolean Whether ot not to add on* attributes to list item
     * @return string
     */
    protected function _renderCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
        $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
    {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();

        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);

        // prepare list item html classes
        $classes = array();
        $classes[] = 'nav-' . $this->_getItemPosition($level);
        $linkClass = '';
        if ($isOutermost && $outermostItemClass) {
            $classes[] = $outermostItemClass;
            $linkClass = ' class="'.$outermostItemClass.'"';
        }
        if ($this->isCategoryActive($category)) {
            $classes[] = 'active';
        }
        if ($isFirst) {
            $classes[] = 'first';
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        if ($hasActiveChildren && !$noEventAttributes) {
             $attributes['onmouseover'] = 'toggleMenu(this,1)';
             $attributes['onmouseout'] = 'toggleMenu(this,0)';
        }

        // assemble list item with attributes
        if ($level != 0) {
        		$htmlLi = '<li';
        		foreach ($attributes as $attrName => $attrValue) {
            		$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
       			}
        		$htmlLi .= '>';
        		$html[] = $htmlLi;
        		$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.' style="background-image:url(/skin/frontend/default/monbento/images/categories/'.$category->getUrlKey().'.png);">';
        		$html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
        		$html[] = '</a>';
				}

        // render children
        $htmlChildren = '';
        $j = 0;
        foreach ($activeChildren as $child) {
            $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                $child,
                ($level + 1),
                ($j == $activeChildrenCount - 1),
                ($j == 0),
                false,
                $outermostItemClass,
                $childrenWrapClass,
                $noEventAttributes
            );
            $j++;
        }
        if (!empty($htmlChildren)) {
            if ($childrenWrapClass) {
                $html[] = '<div class="' . $childrenWrapClass . '">';
            }
            if ($level != 0) $html[] = '<ul class="level' . $level . '">';
            $html[] = $htmlChildren;
            if ($level != 0) $html[] = '</ul>';
            if ($childrenWrapClass) {
                $html[] = '</div>';
            }
        }

        if ($level != 0) $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }
    
    protected function _renderCategoryNewMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
        $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
    {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();

        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);

        // prepare list item html classes
        $classes = array();
        $classes[] = 'nav-' . $this->_getItemPosition($level);
        $linkClass = '';
        if ($isOutermost && $outermostItemClass) {
            $classes[] = $outermostItemClass;
            $linkClass = ' class="'.$outermostItemClass.'"';
        }
        if ($this->isCategoryActive($category)) {
            $classes[] = 'active';
        }
        if ($isFirst) {
            $classes[] = 'first';
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        if ($hasActiveChildren && !$noEventAttributes) {
             $attributes['onmouseover'] = 'toggleMenu(this,1)';
             $attributes['onmouseout'] = 'toggleMenu(this,0)';
        }

        // assemble list item with attributes
        if ($level != 0) {
        		$htmlLi = '<li';
        		foreach ($attributes as $attrName => $attrValue) {
            		$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
       			}
        		$htmlLi .= '>';
        		$html[] = $htmlLi;
        		$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
        		$html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
        		$html[] = '</a>';
				}

        // render children
        $htmlChildren = '';
        $j = 0;
        foreach ($activeChildren as $child) {
            $htmlChildren .= $this->_renderCategoryNewMenuItemHtml(
                $child,
                ($level + 1),
                ($j == $activeChildrenCount - 1),
                ($j == 0),
                false,
                $outermostItemClass,
                $childrenWrapClass,
                $noEventAttributes
            );
            $j++;
        }
        
        if ($level == 1) {
            $loadCat = Mage::getModel('catalog/category')->load($category->getId());
            $visu1 = $loadCat->getData('visuel_1');
            $visu1url = $loadCat->getData('visuel_1_url');
            $visu2 = $loadCat->getData('visuel_2');
            $visu2url = $loadCat->getData('visuel_2_url');
            if (!empty($htmlChildren) or !empty($visu1) or !empty($visu2)) {
                if ($childrenWrapClass) {
                    $html[] = '<div class="' . $childrenWrapClass . '">';
                }
                if (!empty($htmlChildren)) {
                    $html[] = '<div class="childMenuCollection"><h3>'.Mage::helper('catalog')->__('La collection').'</h3><ul class="level' . $level . '">';
                    $html[] = $htmlChildren;
                    $html[] = '</ul></div>';
                }
                $html[] = '<div class="childMenuVisuels">';
                if(!empty($visu1)) {
                    if(!empty($visu1url)) $html[] = '<a href="'.$visu1url.'">';
                    $html[] = '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/catalog/category/'.$visu1.'" alt="" />';
                    if(!empty($visu1url)) $html[] = '</a>';
                }
                if(!empty($visu2)) {
                    if(!empty($visu2url)) $html[] = '<a href="'.$visu2url.'">';
                    $html[] = '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/catalog/category/'.$visu2.'" alt="" />';
                    if(!empty($visu2url)) $html[] = '</a>';
                }
                $html[] = '</div>';
                $attribute_zoom_options = array();
                $attribute_zoom = Mage::getModel('eav/config')->getAttribute('catalog_product','zoom');
                $attribute_zoomCollection = $attribute_zoom->getSource()->getAllOptions(false);
                foreach ($attribute_zoomCollection as $attribute) {
                    $NbProductsOfAttribute = Mage::getModel('catalog/product')->getCollection()
                                                ->addAttributeToSelect("*")
                                                ->addAttributeToFilter(array(array('attribute' => 'zoom', 'finset' => array($attribute["value"]))))
                                                ->addCategoryFilter($loadCat)
                                                ->getSize();
                    if($NbProductsOfAttribute > 0) $attribute_zoom_options[] = array($attribute["value"],$attribute["label"]);
                }
                if(count($attribute_zoom_options)) {
                    $labels = $attribute_zoom->getStoreLabels();
                    $correspondances = array(1 => 1, 2 => 6, 3 => 3, 4 => 8, 5 => 5, 6 => 6, 7 => 1, 8 => 8, 9 => 6, 10 => 6);
                    $html[] = '<div class="childMenuZoom"><h3>'.$labels[$correspondances[Mage::app()->getStore()->getStoreId()]].'</h3><ul class="level' . $level . '">';
                    foreach($attribute_zoom_options as $option) {
                        $urlCode = htmlentities($option[1], ENT_NOQUOTES, 'utf-8');
                        $urlCode = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $urlCode);
                        $html[] = '<li><a href="'.substr($loadCat->getUrl(),0,-5).'/'.strtolower(str_replace(' ', '_', $urlCode)).'.html" title="">'.$option[1].'</a></li>';
                    }
                    $html[] = '</ul></div>';
                }
            
                if ($childrenWrapClass) {
                    $html[] = '</div>';
                }
            }
            $html[] = '</li>';
        } else {
            $html[] = $htmlChildren;
        }

        $html = implode("\n", $html);
        return $html;
    }

    protected function _getClassNameFromCategoryName($category){
				$name = $category->getName();
     		$name = preg_replace('/-{2,}/', '-', preg_replace('/[^a-z-]/', '-', strtolower($name)));
				while ($name && $name{0} == '-') $name = substr($name, 1);
				while ($name && substr($name, -1) == '-') $name = substr($name, 0, -1);
     		return $name;
    }

}