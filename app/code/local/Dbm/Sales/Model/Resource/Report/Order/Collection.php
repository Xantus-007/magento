<?php 

class Dbm_Sales_Model_Resource_Report_Order_Collection extends Mage_Sales_Model_Resource_Report_Order_Collection
{

	/**
     * Get selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();
        if ('month' == $this->_period) {
            $this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = $adapter->getDateExtractSql('period', Varien_Db_Adapter_Interface::INTERVAL_YEAR);
        } else {
            $this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m-%d');
        }

        if (!$this->isTotals()) {
            $this->_selectedColumns = array(
                'period'                         => $this->_periodFormat,
                'orders_count'                   => 'SUM(orders_count)',
                'total_qty_ordered'              => 'SUM(total_qty_ordered)',
                'total_qty_invoiced'             => 'SUM(total_qty_invoiced)',
                'total_income_amount'            => 'SUM(total_income_amount)',
                'total_revenue_amount'           => 'SUM(total_revenue_amount)',
                'total_profit_amount'            => 'SUM(total_profit_amount)',
                'total_invoiced_amount'          => 'SUM(total_invoiced_amount)',
                'total_canceled_amount'          => 'SUM(total_canceled_amount)',
                'total_paid_amount'              => 'SUM(total_paid_amount)',
                'total_refunded_amount'          => 'SUM(total_refunded_amount)',
                'total_tax_amount'               => 'SUM(total_tax_amount)',
                'total_tax_amount_actual'        => 'SUM(total_tax_amount_actual)',
                'total_shipping_amount'          => 'SUM(total_shipping_amount)',
                'total_shipping_amount_actual'   => 'SUM(total_shipping_amount_actual)',
                'total_discount_amount'          => 'SUM(total_discount_amount)',
                'total_discount_amount_actual'   => 'SUM(total_discount_amount_actual)',
                'total_base_cost'   			 => 'SUM(total_base_cost)',
            );
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        return $this->_selectedColumns;
    }

}