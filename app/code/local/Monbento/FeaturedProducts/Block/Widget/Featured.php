<?php
/**
 * Monbento_FeaturedProducts extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Monbento
 * @package    Monbento_FeaturedProducts
 * @copyright  Copyright (c) 2010 Anthony Charrex
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Monbento
 * @package    Monbento_FeaturedProducts
 * @author     Anthony Charrex <anthony.charrax@gmail.com>
 */
 
class Monbento_FeaturedProducts_Block_Widget_Featured extends Monbento_FeaturedProducts_Block_Featured implements Mage_Widget_Block_Interface
{

    /**
     * Internal contructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Retrieve how much products should be displayed.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            return parent::getProductsCount();
        }
        return $this->_getData('products_count');
    }

}

