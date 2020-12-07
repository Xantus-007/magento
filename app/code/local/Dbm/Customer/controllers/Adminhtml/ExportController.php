<?php

class Dbm_Customer_Adminhtml_ExportController extends Mage_Adminhtml_Controller_Action
{
    public function _construct()
    {
        parent::_construct();
        $this->_publicActions[] = 'customerBefore12';
        $this->_publicActions[] = 'customerBetween6and12';
    }
    
    protected function _getStoreName($store)
    {
        switch($store) {
            case 1:
            case 7:
               return "Francais";
            case 2:
            case 6:
            case 9:
            case 10:
                return "Anglais";
            case 3:
                return "Italien";
            case 4:
            case 8:
                return "Espagnol";
            case 5:
                return "Allemand";
            default:
                return "";
            break;
        }
    }
    
    protected function _getStockName($website)
    {
        switch($website) {
            case 1:
               return "Stock SA";
            case 3:
                return "Stock US";
            case 4:
                return "Stock HK";
            case 5:
                return "Stock UK";
            default:
                return "";
            break;
        }
    }
    
    protected function _getCountry($customer)
    {
        $country = "";
        if($billingAddress = $customer->getPrimaryBillingAddress())
        {
            $countryCode = $billingAddress->getCountryId();
            if(!empty($countryCode)) $country = Mage::app()->getLocale()->getCountryTranslation($countryCode);
        }
        
        return $country;
    }

    public function customerBefore12Action()
    {
        ini_set('memory_limit','2G');
        
        //$csv = '"ID";"PAYS";"EMAIL";"NOM COMPLET";"NOM"'."\n";
        $csv = '"ID";"STOCK";"LANGUE";"PAYS";"EMAIL";"NOM COMPLET";"NOM"'."\n";
                
        $customers = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', array('to' => date('Y-m-d', strtotime("-6 month"))))
            ->addAttributeToFilter('nb_order', array('gt' => 0));

        foreach ($customers as $customer)
        {
            $ordersExcerpt = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addAttributeToFilter('created_at', array('from' => date('Y-m-d', strtotime("-12 month"))))
                ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE))
                ->count();
            
            if($ordersExcerpt > 0) continue;
            
            $csv .= '"'.$customer->getID().'";"'.$this->_getStockName($customer->getWebsiteId()).'";"'.$this->_getStoreName($customer->getStoreId()).'";"'.$this->_getCountry($customer).'";"'.$customer->getEmail().'";"'.$customer->getName().'";"'.$customer->getLastname().'"'."\n";
        }

        // pick file mime type, depending on the extension
        $fileMimeType = 'application/csv';
        // set the filename
        $filename   = 'customers_export_12_'.Mage::getSingleton('core/date')->date('Ymd_His') . '.csv';

        // download the file
        return $this->_prepareDownloadResponse($filename, $csv, $fileMimeType .'; charset="utf8"');
    }

    public function customerBetween6and12Action()
    {
        ini_set('memory_limit','2G');
        
        //$csv = '"ID";"PAYS";"EMAIL";"NOM COMPLET";"NOM"'."\n";
        $csv = '"ID";"STOCK";"LANGUE";"PAYS";"EMAIL";"NOM COMPLET";"NOM"'."\n";
                
        $customers = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', array('to' => date('Y-m-d', strtotime("-6 month"))))
            ->addAttributeToFilter('nb_order', array('gt' => 0));

        foreach ($customers as $customer)
        {
            $ordersExcerpt = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addAttributeToFilter('created_at', array('from' => date('Y-m-d', strtotime("-6 month"))))
                ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE))
                ->count();
            
            if($ordersExcerpt > 0) continue;
            
            $ordersTest = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addAttributeToFilter('created_at', array('from' => date('Y-m-d', strtotime("-12 month")), 'to' => date('Y-m-d', strtotime("-6 month"))))
                ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE))
                ->count();
            
            if($ordersTest > 0) {
                $csv .= '"'.$customer->getID().'";"'.$this->_getStockName($customer->getWebsiteId()).'";"'.$this->_getStoreName($customer->getStoreId()).'";"'.$this->_getCountry($customer).'";"'.$customer->getEmail().'";"'.$customer->getName().'";"'.$customer->getLastname().'"'."\n";
            }
        }

        // pick file mime type, depending on the extension
        $fileMimeType = 'application/csv';
        // set the filename
        $filename   = 'customers_export_6_'.Mage::getSingleton('core/date')->date('Ymd_His') . '.csv';

        // download the file
        return $this->_prepareDownloadResponse($filename, $csv, $fileMimeType .'; charset="utf8"');
    }
}