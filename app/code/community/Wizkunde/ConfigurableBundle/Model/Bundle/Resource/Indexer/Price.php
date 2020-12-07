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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Bundle Stock Status Indexer Resource Model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Wizkunde_ConfigurableBundle_Model_Bundle_Resource_Indexer_Price extends Mage_Bundle_Model_Resource_Indexer_Price
{
    /**
     * Calculate bundle product selections price by product type
     *
     * @param int $priceType
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    protected function _calculateBundleSelectionPrice($priceType)
    {
        $write = $this->_getWriteAdapter();

        if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
            $selectionPriceValue = $write->getCheckSql(
                'bsp.selection_price_value IS NULL',
                'bs.selection_price_value',
                'bsp.selection_price_value'
            );
            $selectionPriceType = $write->getCheckSql(
                'bsp.selection_price_type IS NULL',
                'bs.selection_price_type',
                'bsp.selection_price_type'
            );
            $priceExpr = new Zend_Db_Expr(
                $write->getCheckSql(
                    $selectionPriceType . ' = 1',
                    'ROUND(i.price * (' . $selectionPriceValue . ' / 100),2)',
                    $write->getCheckSql(
                        'i.special_price > 0 AND i.special_price < 100',
                        'ROUND(' . $selectionPriceValue . ' * (i.special_price / 100),2)',
                        $selectionPriceValue
                    )
                ) . '* bs.selection_qty'
            );

            $tierExpr = $write->getCheckSql(
                'i.base_tier IS NOT NULL',
                $write->getCheckSql(
                    $selectionPriceType .' = 1',
                    'ROUND(i.base_tier - (i.base_tier * (' . $selectionPriceValue . ' / 100)),2)',
                    $write->getCheckSql(
                        'i.tier_percent > 0',
                        'ROUND(' . $selectionPriceValue
                        . ' - (' . $selectionPriceValue . ' * (i.tier_percent / 100)),2)',
                        $selectionPriceValue
                    )
                ) . ' * bs.selection_qty',
                'NULL'
            );

            $groupExpr = $write->getCheckSql(
                'i.base_group_price IS NOT NULL',
                $write->getCheckSql(
                    $selectionPriceType .' = 1',
                    $priceExpr,
                    $write->getCheckSql(
                        'i.group_price_percent > 0',
                        'ROUND(' . $selectionPriceValue
                        . ' - (' . $selectionPriceValue . ' * (i.group_price_percent / 100)),2)',
                        $selectionPriceValue
                    )
                ) . ' * bs.selection_qty',
                'NULL'
            );
            $priceExpr = new Zend_Db_Expr(
                $write->getCheckSql("{$groupExpr} < {$priceExpr}", $groupExpr, $priceExpr)
            );
        } else {
            $priceExpr = new Zend_Db_Expr(
                $write->getCheckSql(
                    'i.special_price > 0 AND i.special_price < 100',
                    'ROUND(idx.min_price * (i.special_price / 100), 2)',
                    'idx.min_price'
                ) . ' * bs.selection_qty'
            );
            $tierExpr = $write->getCheckSql(
                'i.base_tier IS NOT NULL',
                'ROUND(idx.min_price * (i.base_tier / 100), 2)* bs.selection_qty',
                'NULL'
            );
            $groupExpr = $write->getCheckSql(
                'i.base_group_price IS NOT NULL',
                'ROUND(idx.min_price * (i.base_group_price / 100), 2)* bs.selection_qty',
                'NULL'
            );
            $groupPriceExpr = new Zend_Db_Expr(
                $write->getCheckSql(
                    'i.base_group_price IS NOT NULL AND i.base_group_price > 0 AND i.base_group_price < 100',
                    'ROUND(idx.min_price - idx.min_price * (i.base_group_price / 100), 2)',
                    'idx.min_price'
                ) . ' * bs.selection_qty'
            );
            $priceExpr = new Zend_Db_Expr(
                $write->getCheckSql("{$groupPriceExpr} < {$priceExpr}", $groupPriceExpr, $priceExpr)
            );
        }

        $select = $write->select()
            ->from(
                array('i' => $this->_getBundlePriceTable()),
                array('entity_id', 'customer_group_id', 'website_id')
            )
            ->join(
                array('bo' => $this->getTable('bundle/option')),
                'bo.parent_id = i.entity_id',
                array('option_id')
            )
            ->join(
                array('bs' => $this->getTable('bundle/selection')),
                'bs.option_id = bo.option_id',
                array('selection_id')
            )
            ->joinLeft(
                array('bsp' => $this->getTable('bundle/selection_price')),
                'bs.selection_id = bsp.selection_id AND bsp.website_id = i.website_id',
                array('')
            )
            ->join(
                array('idx' => $this->getIdxTable()),
                'bs.product_id = idx.entity_id AND i.customer_group_id = idx.customer_group_id'
                . ' AND i.website_id = idx.website_id',
                array()
            )
            ->where('i.price_type=?', $priceType)
            ->columns(
                array(
                'group_type'    => $write->getCheckSql(
                    "bo.type = 'select' OR bo.type = 'radio'",
                    '0',
                    '1'
                ),
                'is_required'   => 'bo.required',
                'price'         => $priceExpr,
                'tier_price'    => $tierExpr,
                'group_price'   => $groupExpr,
                )
            );

        $query = $select->insertFromSelect($this->_getBundleSelectionTable());

        $write->query($query);

        return $this;
    }
}