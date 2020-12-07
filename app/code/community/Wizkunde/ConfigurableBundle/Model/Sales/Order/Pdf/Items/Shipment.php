<?php

class Wizkunde_ConfigurableBundle_Model_Sales_Order_Pdf_Items_Shipment extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Shipment
{
    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        /** @var Mage_Tax_Helper_Data $taxHelper */
        $taxHelper = Mage::helper('tax');

        /** @var Mage_Core_Helper_String $stringHelper */
        $stringHelper = Mage::helper('core/string');

        /** @var Mage_Sales_Model_Order $order */
        $order  = $this->getOrder();

        /** @var Mage_Sales_Model_Order_Invoice_Item $item */
        $item   = $this->getItem();

        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $this->_setFontRegular();
        $items = $this->getChilds($item);

        $_prevOptionId = '';
        $drawItems = array();

        /** @var Mage_Sales_Model_Order_Invoice_Item $_item */
        foreach ($items as $_item) {
            $line   = array();

            $attributes = $this->getSelectionAttributes($_item);
            if (is_array($attributes)) {
                $optionId = $attributes['option_id'];
            }
            else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = array(
                    'lines'  => array(),
                    'height' => 15
                );
            }

            if ($_item->getOrderItem()->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[0] = array(
                        'font' => 'italic',
                        'text' => $stringHelper->str_split($attributes['option_label'], 45, true, true),
                        'feed' => 35
                    );

                    $drawItems[$optionId] = array(
                        'lines'  => array($line),
                        'height' => 15
                    );

                    $line = array();

                    $_prevOptionId = $attributes['option_id'];
                }
            }

            /* in case Product name is longer than 80 chars - it is written in a few lines */
            if ($_item->getOrderItem()->getParentItem()) {
                $feed = 40;
                $name = $this->getValueHtml($_item);
            } else {
                $feed = 35;
                $name = $_item->getName();
            }

            $line[] = array(
                'text'  => $stringHelper->str_split($name, 35, true, true),
                'feed'  => $feed
            );

            // draw SKUs
            if (!$_item->getOrderItem()->getParentItem()) {
                $text = array();
                foreach ($stringHelper->str_split($item->getSku(), 17) as $part) {
                    $text[] = $part;
                }

                $line[] = array(
                    'text'  => $text,
                    'feed'  => 255
                );
            }

            // draw prices
            if ($this->canShowPriceInfo($_item)) {
                if ($taxHelper->displaySalesPriceInclTax()) {
                    $price = $order->formatPriceTxt($_item->getPriceInclTax());
                } else {
                    $price = $order->formatPriceTxt($_item->getPrice());
                }

                $line[] = array(
                    'text'  => $price,
                    'feed'  => 395,
                    'font'  => 'bold',
                    'align' => 'right'
                );
                $line[] = array(
                    'text'  => $_item->getQty()*1,
                    'feed'  => 435,
                    'font'  => 'bold',
                );

                $tax = $order->formatPriceTxt($_item->getTaxAmount());
                $line[] = array(
                    'text'  => $tax,
                    'feed'  => 495,
                    'font'  => 'bold',
                    'align' => 'right'
                );

                if ($taxHelper->displaySalesPriceInclTax()) {
                    $row_total = $order->formatPriceTxt($_item->getRowTotalInclTax());
                } else {
                    $row_total = $order->formatPriceTxt($_item->getRowTotal());
                }

                $line[] = array(
                    'text'  => $row_total,
                    'feed'  => 565,
                    'font'  => 'bold',
                    'align' => 'right'
                );
            }

            $drawItems[$optionId]['lines'][] = $line;

            if($_item->getOrderItem()->getParentItem()) {
                if($_item->getOrderItem()->getProduct() !== null) {
                    $configurableOptions = '';
                    if($_item->getOrderItem()->getProduct()->isConfigurable() == true) {
                        $configurableOptions = Mage::helper('bundle/catalog_product_configuration')->getConfigurableAttributes($_item->getOrderItem());

                        $optionData = explode('<br />', $configurableOptions);

                        foreach($optionData as $option) {
                            $drawItems[$optionId]['lines'][] = array(array(
                                'text' => strip_tags($option),
                                'feed' => 45
                            ));
                        }
                    }
                }
            }
        }


        if($_item->getOrderItem()->getParentItem()) {
            if($item->getOrderItem()->getProduct() !== null) {
                $configurableOptions = '';
                if($item->getOrderItem()->getProduct()->isConfigurable() == true) {
                    $configurableOptions = Mage::helper('bundle/catalog_product_configuration')->getConfigurableAttributes($item->getOrderItem());
                    $extraOptions = Mage::helper('bundle/catalog_product_configuration')->getItemOptions($item->getOrderItem());
                }

                $optionValues = Mage::helper('bundle/catalog_product_configuration')->getBundleOrderItemOptions($item->getOrderItem());

                $line[] = array(
                    'text' => 'option',
                    'feed' => 45
                );
                //return $result . $configurableOptions . $extraOptions . $optionValues;
            }
        }


        // custom options
        $options = $item->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $lines = array();
                    $lines[][] = array(
                        'text'  => $stringHelper->str_split(strip_tags($option['label']), 40, true, true),
                        'font'  => 'italic',
                        'feed'  => 35
                    );

                    if ($option['value']) {
                        $text = array();
                        $_printValue = isset($option['print_value'])
                            ? $option['print_value']
                            : strip_tags($option['value']);
                        $values = explode(', ', $_printValue);
                        foreach ($values as $value) {
                            foreach ($stringHelper->str_split($value, 30, true, true) as $_value) {
                                $text[] = $_value;
                            }
                        }

                        $lines[][] = array(
                            'text'  => $text,
                            'feed'  => 40
                        );
                    }

                    $drawItems[] = array(
                        'lines'  => $lines,
                        'height' => 15
                    );
                }
            }
        }

        $page = $pdf->drawLineBlocks($page, $drawItems, array('table_header' => true));

        $this->setPage($page);
    }
}