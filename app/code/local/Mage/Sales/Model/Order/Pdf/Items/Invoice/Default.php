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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Invoice Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{

    /**
     * Draw item line
     */
    public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = array();


        $totalPrice_tax = $item->getRowTotalInclTax();
        $orderItemTaxPercent = $item->getOrderItem()->getTaxPercent();
        $percentTaxAff = number_format($orderItemTaxPercent, 2);
        $priceWithoutTax = $item->getRowTotal() / $item->getQty();

        // draw Product name
        $lines[0] = array(array(
                'text' => Mage::helper('core/string')->str_split($item->getName(), 35, true, true),
                'feed' => 35,
        ));

        // draw SKU
        $lines[0][] = array(
            'text' => Mage::helper('core/string')->str_split($this->getSku($item), 14),
            'feed' => 235,
            'align' => 'left'
        );

        // draw Price Without Tax
        $lines[0][] = array(
            'text' => $order->formatPriceTxt($priceWithoutTax),
            'feed' => 300,
            'font' => 'bold'
        );

        // draw QTY
        $lines[0][] = array(
            'text' => $item->getQty() * 1,
            'feed' => 340
        );

        // draw Total Price Without Tax
        $lines[0][] = array(
            'text' => $order->formatPriceTxt($item->getRowTotal()),
            'feed' => 360,
            'font' => 'bold'
        );

        if ($item->getTaxAmount() >= 0) {
            // draw Tax
            $lines[0][] = array(
                'text' => $order->formatPriceTxt($item->getTaxAmount()),
                'feed' => 415,
                'font' => 'bold'
            );

            // draw Tax rate
            $lines[0][] = array(
                'text' => $percentTaxAff . '%',
                'feed' => 465,
                'font' => 'bold'
            );
        }

        // draw Subtotal
        $lines[0][] = array(
            'text' => $order->formatPriceTxt($totalPrice_tax),
            'feed' => 565,
            'font' => 'bold',
            'align' => 'right'
        );

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $_printValue = $option['print_value'];
                    } else {
                        $_printValue = strip_tags($option['value']);
                    }
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 30, true, true),
                            'feed' => 40
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines' => $lines,
            'height' => 15
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }

}
