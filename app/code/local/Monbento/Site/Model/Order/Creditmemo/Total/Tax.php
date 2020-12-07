<?php

class Monbento_Site_Model_Order_Creditmemo_Total_Tax extends Mage_Sales_Model_Order_Creditmemo_Total_Tax
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $shippingTaxAmount = 0;
        $baseShippingTaxAmount = 0;
        $totalHiddenTax = 0;
        $baseTotalHiddenTax = 0;

        $order = $creditmemo->getOrder();

        list($totalTax, $baseTotalTax) = $this->calculateTaxForRefundAdjustment($creditmemo);

        /** @var $item Mage_Sales_Model_Order_Creditmemo_Item */
        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }
            $orderItemTax = $orderItem->getTaxInvoiced();
            $baseOrderItemTax = $orderItem->getBaseTaxInvoiced();
            $orderItemHiddenTax = $orderItem->getHiddenTaxInvoiced();
            $baseOrderItemHiddenTax = $orderItem->getBaseHiddenTaxInvoiced();
            $orderItemQty = $orderItem->getQtyInvoiced();

            if (($orderItemTax || $orderItemHiddenTax) && $orderItemQty) {
                /**
                 * Check item tax amount
                 */

                $tax = $orderItemTax - $orderItem->getTaxRefunded();
                $baseTax = $baseOrderItemTax - $orderItem->getTaxRefunded();
                $hiddenTax = $orderItemHiddenTax - $orderItem->getHiddenTaxRefunded();
                $baseHiddenTax = $baseOrderItemHiddenTax - $orderItem->getBaseHiddenTaxRefunded();
                if (!$item->isLast()) {
                    $availableQty = $orderItemQty - $orderItem->getQtyRefunded();
                    $tax = $creditmemo->roundPrice($tax / $availableQty * $item->getQty());
                    $baseTax = $creditmemo->roundPrice($baseTax / $availableQty * $item->getQty(), 'base');
                    $hiddenTax = $creditmemo->roundPrice($hiddenTax / $availableQty * $item->getQty());
                    $baseHiddenTax = $creditmemo->roundPrice($baseHiddenTax / $availableQty * $item->getQty(), 'base');
                }

                $item->setTaxAmount($tax);
                $item->setBaseTaxAmount($baseTax);
                $item->setHiddenTaxAmount($hiddenTax);
                $item->setBaseHiddenTaxAmount($baseHiddenTax);

                $totalTax += $tax;
                $baseTotalTax += $baseTax;
                $totalHiddenTax += $hiddenTax;
                $baseTotalHiddenTax += $baseHiddenTax;
            }
        }

        $invoice = $creditmemo->getInvoice();

        if ($invoice) {
            //recalculate tax amounts in case if refund shipping value was changed
            if ($order->getBaseShippingAmount() && $creditmemo->getBaseShippingAmount()) {
                $taxFactor = $creditmemo->getBaseShippingAmount() / $order->getBaseShippingAmount();
                $shippingTaxAmount = $invoice->getShippingTaxAmount() * $taxFactor;
                $baseShippingTaxAmount = $invoice->getBaseShippingTaxAmount() * $taxFactor;
                $totalHiddenTax += $invoice->getShippingHiddenTaxAmount() * $taxFactor;
                $baseTotalHiddenTax += $invoice->getBaseShippingHiddenTaxAmount() * $taxFactor;
                $shippingTaxAmount = $creditmemo->roundPrice($shippingTaxAmount);
                $baseShippingTaxAmount = $creditmemo->roundPrice($baseShippingTaxAmount, 'base');
                $totalHiddenTax = $creditmemo->roundPrice($totalHiddenTax);
                $baseTotalHiddenTax = $creditmemo->roundPrice($baseTotalHiddenTax, 'base');
                $totalTax += $shippingTaxAmount;
                $baseTotalTax += $baseShippingTaxAmount;
            }
        } else {
            $orderShippingAmount = $order->getShippingAmount();
            $baseOrderShippingAmount = $order->getBaseShippingAmount();

            $baseOrderShippingRefundedAmount = $order->getBaseShippingRefunded();

            $shippingTaxAmount = 0;
            $baseShippingTaxAmount = 0;
            $shippingHiddenTaxAmount = 0;
            $baseShippingHiddenTaxAmount = 0;

            $shippingDelta = $baseOrderShippingAmount - $baseOrderShippingRefundedAmount;

            if ($shippingDelta > $creditmemo->getBaseShippingAmount()) {
                $part = $creditmemo->getShippingAmount() / $orderShippingAmount;
                $basePart = $creditmemo->getBaseShippingAmount() / $baseOrderShippingAmount;
                $shippingTaxAmount = $order->getShippingTaxAmount() * $part;
                $baseShippingTaxAmount = $order->getBaseShippingTaxAmount() * $basePart;
                $shippingHiddenTaxAmount = $order->getShippingHiddenTaxAmount() * $part;
                $baseShippingHiddenTaxAmount = $order->getBaseShippingHiddenTaxAmount() * $basePart;
                $shippingTaxAmount = $creditmemo->roundPrice($shippingTaxAmount);
                $baseShippingTaxAmount = $creditmemo->roundPrice($baseShippingTaxAmount, 'base');
                $shippingHiddenTaxAmount = $creditmemo->roundPrice($shippingHiddenTaxAmount);
                $baseShippingHiddenTaxAmount = $creditmemo->roundPrice($baseShippingHiddenTaxAmount, 'base');
            } elseif ($shippingDelta == $creditmemo->getBaseShippingAmount()) {
                $shippingTaxAmount = $order->getShippingTaxAmount() - $order->getShippingTaxRefunded();
                $baseShippingTaxAmount = $order->getBaseShippingTaxAmount() - $order->getBaseShippingTaxRefunded();
                $shippingHiddenTaxAmount = $order->getShippingHiddenTaxAmount()
                    - $order->getShippingHiddenTaxRefunded();
                $baseShippingHiddenTaxAmount = $order->getBaseShippingHiddenTaxAmount()
                    - $order->getBaseShippingHiddenTaxRefunded();
            }
            $totalTax += $shippingTaxAmount;
            $baseTotalTax += $baseShippingTaxAmount;
            $totalHiddenTax += $shippingHiddenTaxAmount;
            $baseTotalHiddenTax += $baseShippingHiddenTaxAmount;
        }

        $allowedTax = $order->getTaxInvoiced() - $order->getTaxRefunded() - $creditmemo->getTaxAmount();
        $allowedBaseTax = $order->getBaseTaxInvoiced() - $order->getBaseTaxRefunded()
            - $creditmemo->getBaseTaxAmount();
        $allowedHiddenTax = $order->getHiddenTaxInvoiced() + $order->getShippingHiddenTaxAmount()
            - $order->getHiddenTaxRefunded() - $order->getShippingHiddenTaxRefunded();
        $allowedBaseHiddenTax = $order->getBaseHiddenTaxInvoiced() + $order->getBaseShippingHiddenTaxAmount()
            - $order->getBaseHiddenTaxRefunded() - $order->getBaseShippingHiddenTaxRefunded();


        $totalTax = min($allowedTax, $totalTax);
        $baseTotalTax = min($allowedBaseTax, $baseTotalTax);
        $totalHiddenTax = min($allowedHiddenTax, $totalHiddenTax);
        $baseTotalHiddenTax = min($allowedBaseHiddenTax, $baseTotalHiddenTax);

        $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $totalTax);
        $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $baseTotalTax);
        $creditmemo->setHiddenTaxAmount($totalHiddenTax);
        $creditmemo->setBaseHiddenTaxAmount($baseTotalHiddenTax);

        $creditmemo->setShippingTaxAmount($shippingTaxAmount);
        $creditmemo->setBaseShippingTaxAmount($baseShippingTaxAmount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $totalTax + $totalHiddenTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseTotalTax + $baseTotalHiddenTax);
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return array
     */
    private function calculateTaxForRefundAdjustment(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        /** @var Mage_Sales_Model_Resource_Order_Item_Collection $orderItems */
        $orderItems = $creditmemo->getOrder()->getItemsCollection();
        $taxPercentage = 0;
        foreach ($orderItems as $item) {
            $taxPercentage = max($taxPercentage, $item->getTaxPercent() / 100);
        }

        $totalAdjustment = $creditmemo->getAdjustmentPositive() - $creditmemo->getAdjustmentNegative();
        $baseTotalAdjustment = $creditmemo->getBaseAdjustmentPositive() - $creditmemo->getBaseAdjustmentNegative();

        // Adjustment values already include tax in my case. Modify calculation if you're entering values without tax
        $totalAdjustmentTax = $totalAdjustment / ($taxPercentage + 1) * $taxPercentage;
        $baseTotalAdjustmentTax = $baseTotalAdjustment / ($taxPercentage + 1) * $taxPercentage;

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalAdjustmentTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalAdjustmentTax);

        return array($totalAdjustmentTax, $baseTotalAdjustmentTax);
    }
}
