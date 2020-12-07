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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order PDF abstract model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Order_Pdf_Abstract extends Varien_Object
{
    public $y;
    /**
     * Item renderers with render type key
     *
     * model    => the model name
     * renderer => the renderer model
     *
     * @var array
     */
    protected $_renderers = array();

    const XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID = 'sales_pdf/invoice/put_order_id';
    const XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID = 'sales_pdf/shipment/put_order_id';
    const XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID = 'sales_pdf/creditmemo/put_order_id';

    /**
     * Zend PDF object
     *
     * @var Zend_Pdf
     */
    protected $_pdf;

    protected $_defaultTotalModel = 'sales/order_pdf_total_default';

    /**
     * Retrieve PDF
     *
     * @return Zend_Pdf
     */
    abstract public function getPdf();

    /**
     * Returns the total width in points of the string using the specified font and
     * size.
     *
     * This is not the most efficient way to perform this calculation. I'm
     * concentrating optimization efforts on the upcoming layout manager class.
     * Similar calculations exist inside the layout manager class, but widths are
     * generally calculated only after determining line fragments.
     *
     * @param string $string
     * @param Zend_Pdf_Resource_Font $font
     * @param float $fontSize Font size in points
     * @return float
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = '"libiconv"' == ICONV_IMPL ? iconv('UTF-8', 'UTF-16BE//IGNORE', $string) : @iconv('UTF-8', 'UTF-16BE', $string);

        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;

    }

    /**
     * Calculate coordinates to draw something in a column aligned to the right
     *
     * @param string $string
     * @param int $x
     * @param int $columnWidth
     * @param Zend_Pdf_Resource_Font $font
     * @param int $fontSize
     * @param int $padding
     * @return int
     */
    public function getAlignRight($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize, $padding = 5)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + $columnWidth - $width - $padding;
    }

    /**
     * Calculate coordinates to draw something in a column aligned to the center
     *
     * @param string $string
     * @param int $x
     * @param int $columnWidth
     * @param Zend_Pdf_Resource_Font $font
     * @param int $fontSize
     * @return int
     */
    public function getAlignCenter($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + round(($columnWidth - $width) / 2);
    }

    protected function insertLogo(&$page, $store = null)
    {
        $image = Mage::getStoreConfig('sales/identity/logo', $store);

        if ($image) {
            $image = str_replace("{{root_dir}}", ".", Mage::getStoreConfig('system/filesystem/media', $store)) . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $image = Zend_Pdf_Image::imageWithPath($image);
                $page->drawImage($image, 25, 800, 125, 825);
            }
        }
        //return $page;
    }

    protected function insertAddress(&$page, $store = null)
    {
        $this->_insertAddressBis($page, $store);

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 5);

        $page->setLineWidth(0.5);
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->drawLine(125, 825, 125, 790);

        $page->setLineWidth(0);
        $this->y = 820;
        foreach (explode("\n", Mage::getStoreConfig('sales/identity/address', $store)) as $value){
            if ($value!=='') {
                $page->drawText(trim(strip_tags($value)), 130, $this->y, 'UTF-8');
                $this->y -=7;
            }
        }
        //return $page;
    }

    /**
     * Format address
     *
     * @param string $address
     * @return array
     */
    protected function _formatAddress($address)
    {
        $return = array();
        foreach (explode('|', $address) as $str) {
            foreach (Mage::helper('core/string')->str_split($str, 65, true, true) as $part) {
                if (empty($part)) {
                    continue;
                }
                $return[] = $part;
            }
        }
        return $return;
    }

    protected function insertOrder(&$page, $obj, $putOrderId = true, $orderInfo = true)
    {
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        /* @var $order Mage_Sales_Model_Order */
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));

        $page->drawRectangle(25, 790, 570, 755);

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page);


        if ($putOrderId) {
            $page->drawText(Mage::helper('sales')->__('Order # ').$order->getRealOrderId(), 35, 770, 'UTF-8');
        }
        //$page->drawText(Mage::helper('sales')->__('Order Date: ') . date( 'D M j Y', strtotime( $order->getCreatedAt() ) ), 35, 760, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false), 35, 760, 'UTF-8');

        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, 755, 275, 730);
        $page->drawRectangle(275, 755, 570, 730);

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
        if ($order->getCustomerId() && $order->getBillingAddress()->getData('country_id') == 'IT') {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            if($customer->getData('fiscal_id')) {
                $billingAddress[] = __('Code fiscal') . ': '. $customer->getData('fiscal_id');
            }
        }
        
        /* Payment */
        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();

        $payment = preg_split('#{{pdf_row_separator}}|<br />#i', $paymentInfo);

        $newPayment = array();
        foreach($payment as $key => $value)
        {
            if(strlen($value) > 75)
            {
                $newPayment += explode("\n", wordwrap($value, 75));
            }
            else
            {
                $newPayment[] = $value;
            }
        }
        $payment = $newPayment;

        foreach ($payment as $key=>&$value){
            if (strip_tags(trim($value))==''){
                unset($payment[$key]);
            }
            else
            {
                $value = trim($value);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));

            $shippingMethod  = $order->getShippingDescription();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $page->drawText(Mage::helper('sales')->__('SOLD TO:'), 35, 740 , 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('SHIP TO:'), 285, 740 , 'UTF-8');
        }
        else {
            $page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, 740 , 'UTF-8');
        }

        if (!$order->getIsVirtual()) {
            $y = 730 - (max(count($billingAddress), count($shippingAddress)) * 10 + 5);
        }
        else {
            $y = 730 - (count($billingAddress) * 10 + 5);
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, 730, 570, $y);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $this->y = 720;

        foreach ($billingAddress as $valueBilling){
            if ($valueBilling!=='') {
                $page->drawText(strip_tags(ltrim($valueBilling)), 35, $this->y, 'UTF-8');
                $this->y -=10;
            }
        }

        if($orderInfo == true) {
            if (!$order->getIsVirtual()) {
                $this->y = 720;
                foreach ($shippingAddress as $valueShipping){
                    if ($valueShipping!=='') {
                        $page->drawText(strip_tags(ltrim($valueShipping)), 285, $this->y, 'UTF-8');
                        $this->y -=10;
                    }

                }

                $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $this->y, 275, $this->y-25);
                $page->drawRectangle(275, $this->y, 570, $this->y-25);

                $this->y -=15;
                $this->_setFontBold($page);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');

                $this->y -=10;
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

                $this->_setFontRegular($page);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

                $paymentLeft = 35;
                $yPayments   = $this->y - 15;
            }
            else {
                $yPayments   = 720;
                $paymentLeft = 285;
            }

            foreach ($payment as $value){
                if (trim($value)!=='') {
                    $page->drawText(strip_tags(trim($value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -=10;
                }
            }
        } else {
            $this->y = 720;
            if(isset($shippingAddress))
            {
                foreach ($shippingAddress as $value){
                    if ($value!=='') {
                        $page->drawText(strip_tags(ltrim($value)), 285, $this->y, 'UTF-8');
                        $this->y -=10;
                    }

                }
            }
        }

        if (!$order->getIsVirtual() && $orderInfo == true) {
            $this->y -=15;

            $page->drawText(strip_tags($shippingMethod), 285, $this->y, 'UTF-8');

            $yShipments = $this->y;


            $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " " . $order->formatPriceTxt($order->getShippingAmount()) . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments-7, 'UTF-8');
            $yShipments -=10;

            $tracks = array();
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(380, $yShipments, 380, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Number'), 385, $yShipments - 7, 'UTF-8');

                $yShipments -=17;
                $this->_setFontRegular($page, 6);
                foreach ($tracks as $track) {

                    $CarrierCode = $track->getCarrierCode();
                    if ($CarrierCode!='custom')
                    {
                        $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
                        $carrierTitle = $carrier->getConfigData('title');
                    }
                    else
                    {
                        $carrierTitle = Mage::helper('sales')->__('Custom Value');
                    }

                    //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
                    $truncatedTitle = substr($track->getTitle(), 0, 45) . (strlen($track->getTitle()) > 45 ? '...' : '');
                    //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
                    $page->drawText($truncatedTitle, 300, $yShipments , 'UTF-8');
                    $page->drawText($track->getNumber(), 395, $yShipments , 'UTF-8');
                    $yShipments -=7;
                }
            } else {
                $yShipments -= 7;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $this->y + 15, 25, $currentY);
            $page->drawLine(25, $currentY, 570, $currentY);
            $page->drawLine(570, $currentY, 570, $this->y + 15);

            $this->y = $currentY;
            $this->y -= 15;

            $this->_insertBas($page, $order);
        }
    }

        /**
     * Insert title and number for concrete document type
     *
     * @param  Zend_Pdf_Page $page
     * @param  string $text
     * @return void
     */
    protected function insertDocumentNumber(Zend_Pdf_Page $page, $text)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page, 10);
        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText($text, 35, $docHeader[1] - 15, 'UTF-8');
    }

    protected function _sortTotalsList($a, $b) {
        if (!isset($a['sort_order']) || !isset($b['sort_order'])) {
            return 0;
        }

        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }

        return ($a['sort_order'] > $b['sort_order']) ? 1 : -1;
    }

    protected function _getTotalsList($source)
    {
        $totals = Mage::getConfig()->getNode('global/pdf/totals')->asArray();
        usort($totals, array($this, '_sortTotalsList'));
        $totalModels = array();
        foreach ($totals as $index => $totalInfo) {
            if (!empty($totalInfo['model'])) {
                $totalModel = Mage::getModel($totalInfo['model']);
                if ($totalModel instanceof Mage_Sales_Model_Order_Pdf_Total_Default) {
                    $totalInfo['model'] = $totalModel;
                } else {
                    Mage::throwException(
                        Mage::helper('sales')->__('PDF total model should extend Mage_Sales_Model_Order_Pdf_Total_Default')
                    );
                }
            } else {
                $totalModel = Mage::getModel($this->_defaultTotalModel);
            }
            $totalModel->setData($totalInfo);
            $totalModels[] = $totalModel;
        }

        return $totalModels;
    }

    protected function insertTotals($page, $source, $totauxCustom, $isCreditMemo = false){
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );

        $firstLine = 1;
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    if($firstLine == 1) {
                        $this->y +=5;
                        $page->setFillColor(new Zend_Pdf_Color_RGB(0.96, 0.96, 0.96));
                        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                        $page->setLineWidth(0.5);

                        $page->drawRectangle(25, $this->y, 570, $this->y-15);
                        $this->y -=10;

                        $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
                        $page->drawText($totalData['label'], 35, $this->y, 'UTF-8');
                        $page->drawText($totalData['amount'], 535, $this->y, 'UTF-8');

                        $this->y -=15;
                    } else {
                        if($firstLine == 2) {
                            unset($lineBlock['lines']);

                            $totalHTTax1 = 0;
                            $totalHTTax2 = 0;
                            $totalTVA1 = 0;
                            $totalTVA2 = 0;
                            $totalTaxRate1 = 0;
                            $totalTaxRate2 = 0;
                            $total1 = 0;
                            $total2 = 0;
                            foreach ($totauxCustom as $totalItem) {
                                if($totalTaxRate1 == 0) $totalTaxRate1 = $totalItem['Rate'];
                                if($totalTaxRate1 != 0 and $totalTaxRate2 == 0 and $totalTaxRate1 != $totalItem['Rate']) $totalTaxRate2 = $totalItem['Rate'];
                                switch($totalItem['Rate']) {
                                    case $totalTaxRate1:
                                        $totalHTTax1 += $totalItem['price_ht'];
                                        $totalTVA1 += $totalItem['TVA'];
                                        $total1 += $totalItem['TVA'] + $totalItem['price_ht'];
                                    break;
                                    case $totalTaxRate2:
                                        $totalHTTax2 += $totalItem['price_ht'];
                                        $totalTVA2 += $totalItem['TVA'];
                                        $total2 += $totalItem['TVA'] + $totalItem['price_ht'];
                                    break;
                                }
                            }



                            if($totalTaxRate1 != 0) {
                                $lineBlock['lines'][] = array(
                                    array(
                                        'text'      => Mage::helper('sales')->__('Total products with tax percent ').$totalTaxRate1.'%',
                                        'feed'      => 35,
                                        'align'     => 'left'
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($totalHTTax1),
                                        'feed'      => 360
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($totalTVA1),
                                        'feed'      => 415
                                    ),
                                    array(
                                        'text'      => $totalTaxRate1.'%',
                                        'feed'      => 465
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($total1),
                                        'feed'      => 535
                                    ),
                                );
                            }

                            if($totalTaxRate2 != 0) {
                                $lineBlock['lines'][] = array(
                                    array(
                                        'text'      => Mage::helper('sales')->__('Total products with tax percent ').$totalTaxRate2.'%',
                                        'feed'      => 35,
                                        'align'     => 'left'
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($totalHTTax2),
                                        'feed'      => 360
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($totalTVA2),
                                        'feed'      => 415
                                    ),
                                    array(
                                        'text'      => $totalTaxRate2.'%',
                                        'feed'      => 465
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($total2),
                                        'feed'      => 535
                                    ),
                                );
                            }

                            $addressOrder = ($order->getShippingAddress()) ?: $order->getBillingAddress();
                            $TaxRequest  = new Varien_Object();
                            $TaxRequest->setCountryId($addressOrder->getCountryId());
                            $TaxRequest->setStore(Mage::app()->getStore());
                            $TaxRequest->setCustomerClassId(6);
                            $TaxRequest->setProductClassId(2);
                            $taxCalculationModel = Mage::getSingleton('tax/calculation');
                            $rate = $taxCalculationModel->getRate($TaxRequest);

                            $newTotalHT = $totalHTTax2 + $totalHTTax1;

                            $subtotalAfterDiscountTTC = $total2 + $total1 + $order->getBaseDiscountAmount();
                            $subtotalTaxes            = $order->getTaxAmount() - $order->getShippingTaxAmount();
                            $subtotalAfterDiscountHT  = $subtotalAfterDiscountTTC - $subtotalTaxes;

                            if($order->getBaseDiscountAmount() != 0) {
                                $percentCumul = array();
                                $collectedRuleIds = array();
                                $itemRules = array();
                                foreach ($order->getAllVisibleItems() as $orderItem) {
                                    if ($orderItem->getAppliedRuleIds()) {
                                        $itemRules[$orderItem->getId()] = explode(',', $orderItem->getAppliedRuleIds());
                                        $collectedRuleIds = array_merge($collectedRuleIds, $itemRules[$orderItem->getId()]);
                                    }
                                }
                                $collectedRuleIds = array_unique($collectedRuleIds);
                                if ($collectedRuleIds) {
                                    foreach($collectedRuleIds as $rule_id) {
                                        $rule = Mage::getModel('salesrule/rule')->load($rule_id);
                                        $percentRule = $rule->getDiscountAmount();
                                        $type = $rule->getSimpleAction();
                                        $newTotalHT = ($type == 'by_percent') ? round($newTotalHT-($newTotalHT*$percentRule/100), 2, PHP_ROUND_HALF_DOWN) : round($newTotalHT-($percentRule*$rate/100), 2, PHP_ROUND_HALF_DOWN);
                                    }
                                }

                                $lineBlock['lines'][] = array(
                                    array(
                                        'text'      => Mage::helper('sales')->__('Discount'),
                                        'feed'      => 35,
                                        'align'     => 'left'
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($order->getBaseDiscountAmount()),
                                        'feed'      => 535
                                    ),
                                );
                            }

                            if($isCreditMemo) {
                                $lineBlock['lines'][] = array(
                                    array(
                                        'text'      => Mage::helper('sales')->__('Subtotal'),
                                        'feed'      => 35,
                                        'align'     => 'left'
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($totalHTTax1 + $totalHTTax2 /*END*/),// - ($order->getTaxAmount() - $order->getShippingTaxAmount())),
                                        'feed'      => 360
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($totalTVA1 + $totalTVA2/*END*/), //- (-$order->getBaseDiscountAmount() + ($order->getBaseDiscountAmount() * 0.833))),
                                        'feed'      => 415
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($total2 + $total1 + $order->getBaseDiscountAmount()),
                                        'feed'      => 535
                                    ),
                                );
                            } else {
                                $lineBlock['lines'][] = array(
                                    array(
                                        'text'      => Mage::helper('sales')->__('Subtotal'),
                                        'feed'      => 35,
                                        'align'     => 'left'
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($subtotalAfterDiscountHT /*END*/),// - ($order->getTaxAmount() - $order->getShippingTaxAmount())),
                                        'feed'      => 360
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($subtotalTaxes/*END*/), //- (-$order->getBaseDiscountAmount() + ($order->getBaseDiscountAmount() * 0.833))),
                                        'feed'      => 415
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($subtotalAfterDiscountTTC),
                                        'feed'      => 535
                                    ),
                                );
                            }

                            if($isCreditMemo) {
                                if($source->getShippingAmount() > 0) {
                                    $lineBlock['lines'][] = array(
                                        array(
                                            'text'      => Mage::helper('sales')->__('Shipping & Handling total'),
                                            'feed'      => 35,
                                            'align'     => 'left'
                                        ),
                                        array(
                                            'text'      => $order->formatPriceTxt($source->getShippingAmount()),
                                            'feed'      => 360
                                        ),
                                        array(
                                            'text'      => $order->formatPriceTxt($source->getShippingTaxAmount()),
                                            'feed'      => 415
                                        ),
                                        array(
                                            'text'      => round(((($source->getShippingAmount()+$source->getShippingTaxAmount()) / $source->getShippingAmount() - 1) * 100),0).'%',
                                            'feed'      => 465
                                        ),
                                        array(
                                            'text'      => $order->formatPriceTxt($source->getShippingAmount()+$source->getShippingTaxAmount()),
                                            'feed'      => 535
                                        ),
                                    );
                                }
                            } else {
                                if($order->getShippingAmount() > 0) {
                                    $lineBlock['lines'][] = array(
                                        array(
                                            'text'      => Mage::helper('sales')->__('Shipping & Handling total'),
                                            'feed'      => 35,
                                            'align'     => 'left'
                                        ),
                                        array(
                                            'text'      => $order->formatPriceTxt($order->getShippingAmount()),
                                            'feed'      => 360
                                        ),
                                        array(
                                            'text'      => $order->formatPriceTxt($order->getShippingTaxAmount()),
                                            'feed'      => 415
                                        ),
                                        array(
                                            'text'      => round(((($order->getShippingAmount()+$order->getShippingTaxAmount()) / $order->getShippingAmount() - 1) * 100),0).'%',
                                            'feed'      => 465
                                        ),
                                        array(
                                            'text'      => $order->formatPriceTxt($order->getShippingAmount()+$order->getShippingTaxAmount()),
                                            'feed'      => 535
                                        ),
                                    );
                                }
                            }

                            if($isCreditMemo) {
                                if($order->getAdjustmentNegative() != 0) {
                                    $lineBlock['lines'][] = array(
                                        array(
                                            'text'      => Mage::helper('sales')->__('Adjustment Fee'),
                                            'feed'      => 35,
                                            'align'     => 'left'
                                        ),
                                        array(
                                            'text'      => $order->formatPriceTxt($order->getAdjustmentNegative()),
                                            'feed'      => 535
                                        ),
                                    );
                                }

                                if($order->getAdjustmentPositive() != 0) {
                                    $lineBlock['lines'][] = array(
                                        array(
                                            'text'      => Mage::helper('sales')->__('Adjustment Refund'),
                                            'feed'      => 35,
                                            'align'     => 'left'
                                        ),
                                        array(
                                            'text'      => $order->formatPriceTxt($source->getAdjustmentPositive() - ($source->getTaxAmount() - ($totalTVA1 + $totalTVA2 + $source->getShippingTaxAmount()))),
                                            'feed'      => 360
                                        ),
                                        array(
                                            'text'      => $order->formatPriceTxt($source->getTaxAmount() - ($totalTVA1 + $totalTVA2 + $source->getShippingTaxAmount())),
                                            'feed'      => 415
                                        ),
                                        array(
                                            'text'      => $order->formatPriceTxt($source->getAdjustmentPositive()),
                                            'feed'      => 535
                                        ),
                                    );
                                }
                            }

                            if($isCreditMemo) {
                                $lineBlock['lines'][] = array(
                                    array(
                                        'text'      => Mage::helper('sales')->__('Total'),
                                        'feed'      => 35,
                                        'align'     => 'left'
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($subtotalAfterDiscountHT + $source->getShippingAmount() + ($source->getAdjustmentPositive() - ($source->getTaxAmount() - ($totalTVA1 + $totalTVA2 + $source->getShippingTaxAmount())))),// - $order->getTaxAmount()),
                                        'feed'      => 360
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($source->getTaxAmount()/*END*/), // - (-$order->getBaseDiscountAmount() + ($order->getBaseDiscountAmount() * 0.833))),
                                        'feed'      => 415
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($total2 + $total1 + $order->getBaseDiscountAmount() + $source->getShippingAmount() + $source->getShippingTaxAmount() - $order->getAdjustmentNegative() + $order->getAdjustmentPositive()),
                                        'feed'      => 535
                                    ),
                                );
                            } else {
                                $lineBlock['lines'][] = array(
                                    array(
                                        'text'      => Mage::helper('sales')->__('Total'),
                                        'feed'      => 35,
                                        'align'     => 'left'
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($subtotalAfterDiscountHT + $order->getShippingAmount()/*END*/),// - $order->getTaxAmount()),
                                        'feed'      => 360
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($order->getTaxAmount()/*END*/),// - (-$order->getBaseDiscountAmount() + ($order->getBaseDiscountAmount() * 0.833))),
                                        'feed'      => 415
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($subtotalAfterDiscountTTC + $order->getShippingAmount() + $order->getShippingTaxAmount() - $order->getAdjustmentNegative() + $order->getAdjustmentPositive()),
                                        'feed'      => 535
                                    ),
                                );
                            }

                            if($isCreditMemo) {
                                $lineBlock['lines'][] = array(
                                    array(
                                        'text'      => Mage::helper('sales')->__('Creditmemo'),
                                        'feed'      => 465,
                                        'font'      => 'bold'
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($total2 + $total1 + $source->getShippingAmount() + $source->getShippingTaxAmount() + $order->getBaseDiscountAmount() - $order->getAdjustmentNegative() + $order->getAdjustmentPositive()),
                                        'feed'      => 535
                                    ),
                                );
                            } else {
                                $lineBlock['lines'][] = array(
                                    array(
                                        'text'      => Mage::helper('sales')->__('Total due'),
                                        'feed'      => 465,
                                        'font'      => 'bold'
                                    ),
                                    array(
                                        'text'      => $order->formatPriceTxt($total2 + $total1 + $order->getShippingAmount() + $order->getShippingTaxAmount() + $order->getBaseDiscountAmount() - $order->getAdjustmentNegative() + $order->getAdjustmentPositive()),
                                        'feed'      => 535
                                    ),
                                );
                            }
                        }
                    }
                    $firstLine++;
                }
            }
        }

        $y = $this->y - ((count($lineBlock['lines']) - 1) * 15) - 55;

        if($y < 15) {
            $page = $this->newPage(array('totals_header' => true));
        } else {
            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y -15);
            $this->y -=10;

            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            if($isCreditMemo) {
                $page->drawText(Mage::helper('sales')->__('Total Refund'), 35, $this->y, 'UTF-8');
            } else {
                $page->drawText(Mage::helper('sales')->__('Total Invoiced'), 35, $this->y, 'UTF-8');
            }
            $page->drawText(Mage::helper('sales')->__('Subtotal(ex)'), 360, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Tax Amount'), 415, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Tax Percent'), 465, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Subtotal(inc)'), 515, $this->y, 'UTF-8');

            $this->y -=15;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        }

        $y = $this->y - ((count($lineBlock['lines']) - 1) * 15) - 5;

        $page->drawRectangle(25, $this->y +10, 570, $y + 15, $fillType = Zend_Pdf_Page::SHAPE_DRAW_STROKE);

        $page = $this->drawLineBlocks($page, array($lineBlock));

        return $page;
    }

    protected function _parseItemDescription($item)
    {
        $matches = array();
        $description = $item->getDescription();
        if (preg_match_all('/<li.*?>(.*?)<\/li>/i', $description, $matches)) {
            return $matches[1];
        }

        return array($description);
    }

    /**
     * Before getPdf processing
     *
     */
    protected function _beforeGetPdf() {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
    }

    /**
     * Calculate address height
     *
     * @param  array $address
     * @return int Height
     */
    protected function _calcAddressHeight($address)
    {
        $y = 0;
        foreach ($address as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 55, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $y += 15;
                }
            }
        }
        return $y;
    }

    /**
     * After getPdf processing
     *
     */
    protected function _afterGetPdf() {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(true);
    }

    protected function _formatOptionValue($value, $order)
    {
        $resultValue = '';
        if (is_array($value)) {
            if (isset($value['qty'])) {
                $resultValue .= sprintf('%d', $value['qty']) . ' x ';
            }

            $resultValue .= $value['title'];

            if (isset($value['price'])) {
                $resultValue .= " " . $order->formatPrice($value['price']);
            }
            return  $resultValue;
        } else {
            return $value;
        }
    }

    protected function _initRenderer($type)
    {
        $node = Mage::getConfig()->getNode('global/pdf/'.$type);
        foreach ($node->children() as $renderer) {
            $this->_renderers[$renderer->getName()] = array(
                'model'     => (string)$renderer,
                'renderer'  => null
            );
        }
    }

    /**
     * Retrieve renderer model
     *
     * @throws Mage_Core_Exception
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    protected function _getRenderer($type)
    {
        if (!isset($this->_renderers[$type])) {
            $type = 'default';
        }

        if (!isset($this->_renderers[$type])) {
            Mage::throwException(Mage::helper('sales')->__('Invalid renderer model'));
        }

        if (is_null($this->_renderers[$type]['renderer'])) {
            $this->_renderers[$type]['renderer'] = Mage::getSingleton($this->_renderers[$type]['model']);
        }

        return $this->_renderers[$type]['renderer'];
    }

    /**
     * Public method of protected @see _getRenderer()
     *
     * Retrieve renderer model
     *
     * @param string $type
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function getRenderer($type)
    {
        return $this->_getRenderer($type);
    }

    /**
     * Draw Item process
     *
     * @param Varien_Object $item
     * @param Zend_Pdf_Page $page
     * @param Mage_Sales_Model_Order $order
     * @return Zend_Pdf_Page
     */
    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order)
    {
        $type = $item->getOrderItem()->getProductType();
        $renderer = $this->_getRenderer($type);
        $renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);

        $renderer->draw();

        return $renderer->getPage();
    }

    protected function _setFontRegular($object, $size = 7)
    {
        //$font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/code20000Font/CODE2000.TTF');
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $object->setFont($font, $size);
        return $font;
    }

    protected function _setFontBold($object, $size = 7)
    {
        //$font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/code20000Font/CODE2000.TTF');
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $object->setFont($font, $size);
        return $font;
    }

    protected function _setFontItalic($object, $size = 7)
    {
        //$font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/code20000Font/CODE2000.TTF');
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set PDF object
     *
     * @param Zend_Pdf $pdf
     * @return Mage_Sales_Model_Order_Pdf_Abstract
     */
    protected function _setPdf(Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Retrieve PDF object
     *
     * @throws Mage_Core_Exception
     * @return Zend_Pdf
     */
    protected function _getPdf()
    {
        if (!$this->_pdf instanceof Zend_Pdf) {
            Mage::throwException(Mage::helper('sales')->__('Please define PDF object before using.'));
        }

        return $this->_pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        $pageSize = !empty($settings['page_size']) ? $settings['page_size'] : Zend_Pdf_Page::SIZE_A4;
        $page = $this->_getPdf()->newPage($pageSize);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;

        return $page;
    }

    /**
     * Draw lines
     *
     * draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param Zend_Pdf_Page $page
     * @param array $draw
     * @param array $pageSettings
     * @throws Mage_Core_Exception
     * @return Zend_Pdf_Page
     */
    public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array())
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array.'));
            }
            $lines  = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 7 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    }
                    else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                }
                                else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                        }
                        $page->drawText($part, $feed, $this->y-$top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }

    protected function _insertBas(&$page, $order)
    {
        if ($order->getStoreName(1) == 'bentob2b') {
            $paiement30j = false;
            if ($order->getCustomerId()) {
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                if($customer->getData('paiement30j')) {
                    $paiement30j = true;
                }
            }
            if ($paiement30j) {
                $date = date('j/m/Y', strtotime($order->getCreatedAt() . ' +37 day'));
                $page->drawText('Paiement à 30 jour', 30, $this->y-72, 'UTF-8');
                $page->drawText('Echéance du paiement : ' . $date, 30, $this->y-80, 'UTF-8');
            }
            else {
            $date = date('j/m/Y', strtotime($order->getCreatedAt() . ' +7 day'));
            $page->drawText('Echéance du paiement : ' . $date, 30, $this->y-80, 'UTF-8');
            }

            $lines[] = "Conditions de règlement :";
            $lines[] = '';
            $lines[] = 'Par virement sur le compte :';
            $lines[] = 'CIC grand clermont';
            $lines[] = 'RIB 10096 18550 00035407101 12';
            $lines[] = 'IBAN FR 76 1009 6185 5000 0354 0710 112 BIC (=swift) CMCIFRPP';
                $lines[] = '';
                $lines[] = 'Ou par chèque à l’ordre de monbento à envoyer à l’adresse suivante :';
                $lines[] = 'MONBENTO';
                $lines[] = 'ZI Les Gravanches';
                $lines[] = '10 rue Jacques Mailhot';
                $lines[] = '63100 Clermont Ferrand';
            $lines[] = '';
            $lines[] = '';
            $lines[] = "Pénalités de retard (taux annuel) : 8 %, pas d’escompte pour paiement anticipé.";
            $lines[] = "RESERVE DE PROPRIETE : Nous nous réservons la propriété des marchandises jusqu'au paiement du prix par l'acheteur. Notre droit de revendication porte aussi bien sur les";
            $lines[] = "marchandises que sur leur prix si elles ont déjà été revendues (Loi du 12 mai 1980). Les marchandises resteront la propriété du vendeur jusqu’au paiement intégral de leur";
            $lines[] = "prix, mais l’acheteur en deviendra responsable dès leur remise matérielle, le transfert de possession entraînant celui des risques. L’acheteur s’engage donc à souscrire dès";
            $lines[] = "la signature du présent document, un contrat d’assurance garantissant les risques de perte, vol ou destruction des marchandises désignées.";
            foreach($lines as $key=>$line) {
                $page->drawText($line, 30, $this->y-100-($key*8), 'UTF-8');
            }
            }
    }

    protected function _insertAddressBis(&$page, $store = null)
    {
      if ($store->getWebsite()->getCode() == 'bentob2b') {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 5);

        $page->setLineWidth(0.5);
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->drawLine(225, 825, 225, 790);

        $page->setLineWidth(0);
        $this->y = 820;

        $lines[] = 'Capital : 62 070 €';
        $lines[] = 'R.C.S. : Clermont-Ferrand';
        $lines[] = 'SIRET : 511 239 444 00017';
        $lines[] = 'N° TVA : FR31 511 239 444';
        $lines[] = 'Mail : pro@monbento.com';

        foreach ($lines as $value){
          if ($value!=='') {
            $page->drawText(trim(strip_tags($value)), 230, $this->y, 'UTF-8');
            $this->y -=7;
          }
        }
      }
    }
}
