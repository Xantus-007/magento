<?php
/**
 * The Magento Developer
 * http://themagentodeveloper.com
 *
 * @category   Sellry
 * @package    Sellry_OrderSync
 * @version    0.1.3
 */

class Sellry_OrderSync_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_logFilename =        'orderSync.log';
    protected $_generalConfigPath =  'ordersync_settings/general';
    protected $_shippingConfigPath = 'ordersync_settings/shipping';
    protected $_authConfigPath =     'ordersync_settings/auth';
//    protected $_emailConfigPath =     'ordersync_settings/email';    

    protected $_upsMethods = array(
        '01' => 'Next Day',
        '02' => '2nd Day Air',
        '03' => 'Ground',
        '07' => 'Worldwide Express',
        '08' => 'Worldwide Expedited',
        '11' => 'Standard',
        '12' => '3-Day Select',
        '13' => 'Next Day Air Saver',
        '14' => 'Next Day Air Early AM',
        '54' => 'Worldwide ExpressSM',
        '59' => '2nd Day Air Saver'
    );

    protected $_fedexMethods = array(
        'SMART_POST'                          => 'SmartPost',
        'FEDEX_2_DAY'                         => '2 Day',
        'FEDEX2DAY'                           => '2 Day',
        'FEDEX_EXPRESS_SAVER'                 => 'Express Saver',
        'FEDEXEXPRESSSAVER'                   => 'Express Saver',
        'FEDEX_GROUND'                        => 'Ground',
        'FEDEXGROUND'                         => 'Ground',
        'GROUNDHOMEDELIVERY'                  => 'Ground Home',
        'GROUND_HOME_DELIVERY'                => 'Ground Home',
        'FIRSTOVERNIGHT'                      => 'First Overnight',
        'FIRST_OVERNIGHT'                     => 'First Overnight',
        'PRIORITYOVERNIGHT'                   => 'Priority Overnight',
        'PRIORITY_OVERNIGHT'                  => 'Priority Overnight',
        'STANDARD_OVERNIGHT'                  => 'Standard Overnight',
        'STANDARDOVERNIGHT'                   => 'Standard Overnight',
        'INTERNATIONAL_FIRST'                 => 'International First',
        'INTERNATIONALFIRST'                  => 'International First',
        'INTERNATIONAL_PRIORITY'              => 'International Priority',
        'INTERNATIONALPRIORITY'               => 'International Priority',
        'INTERNATIONALECONOMY'                => 'International Economy',
        'INTERNATIONAL_ECONOMY'               => 'International Economy',
        'INTERNATIONAL_GROUND'                => 'International Ground',
        'FEDEX1DAYFREIGHT'                    => '1 Day Freight',
        'FEDEX_1_DAY_FREIGHT'                 => '1 Day Freight',
        'FEDEX2DAYFREIGHT'                    => '2 Day Freight',
        'FEDEX_2_DAY_FREIGHT'                 => '2 Day Freight',
        'FEDEX3DAYFREIGHT'                    => '3 Day Freight',
        'FEDEX_3_DAY_FREIGHT'                 => '3 Day Freight',
    );

    protected $_uspsMethods = array(
        'First-Class Mail International Large Envelope'          => 'First Class Mail Intl Large Env. (Flat)',
        'First-Class Mail International Letters'                 => 'First Class Mail Intl Letter',
        'First-Class Mail International Package'                 => 'First Class Mail Intl Package',
        'First-Class'                                            => 'First Class Mail',
        'First-Class Mail'                                       => 'First Class Mail',
        'First-Class Mail Flat'                                  => 'First Class Large Envelope (Flat)',
        'First-Class Mail Letter'                                => 'First Class Letter',
        'Parcel Post'                                            => 'Parcel Post',
        'Express Mail'                                           => 'Express Mail',
        'Express Mail International'                             => 'Express Mail Intl',
        'Express Mail International Flat Rate Envelope'          => 'Express Mail Intl Flat Rate Envelope',
        'Priority Mail'                                          => 'Priority Mail',
        'Priority Mail Small Flat Rate Box'                      => 'Priority Mail Small Flat Rate Box',
        'Priority Mail Medium Flat Rate Box'                     => 'Priority Mail Medium Flat Rate Box',
        'Priority Mail Large Flat Rate Box'                      => 'Priority Mail Large Flat Rate Box',
        'Priority Mail Flat Rate Box'                            => 'Priority Mail Small Flat Rate Box',
        'Priority Mail Flat Rate Envelope'                       => 'Priority Mail Flat Rate Envelope',
        'Priority Mail International'                            => 'Priority Mail Intl',
        'Priority Mail International Flat Rate Envelope'         => 'Priority Mail Intl Flat Rate Envelope',
        'Priority Mail International Small Flat Rate Box'        => 'Priority Mail Intl Small Flat Rate Box',
        'Priority Mail International Medium Flat Rate Box'       => 'Priority Mail Intl Medium Flat Rate Box',
        'Priority Mail International Large Flat Rate Box'        => 'Priority Mail Intl Large Flat Rate Box'
    );

    public function getLogLocation() {
        return getcwd() . DS . 'var' . DS . 'log' . DS;
    }

    public function getLogFilename() {
        return $this->_logFilename;
    }

    //@todo: throw off Mage::log and use something else
    public function logMessage($message) {
        Mage::log($message, 6, $this->_logFilename);
    }

    public function getNiceShippingMethod($order) {

        $niceShippingInfo = array();

        $shippingDescription = $order->getShippingDescription();
        $shippingMethod = $order->getShippingMethod();

        $carrierModel = $order->getShippingCarrier();
        if ($carrierModel) {

            $_carrierCode = $carrierModel->getCarrierCode();
            $_methodCode = str_replace($_carrierCode . '_', '', $shippingMethod);

            $methodTitle = $carrierModel->getConfigData('title');
            $methodName = $carrierModel->getConfigData('name');

            switch($_carrierCode) {
                case 'usps':
                case 'ups':
                case 'fedex':
                case 'fedexsoap':
                    $niceShippingInfo = $this->_tryGetExactMethod($_carrierCode, $_methodCode);
                    break;

                case 'freeshipping':
                    $niceShippingInfo = $this->_getConfigMethod('freemethod');
                    if (count($niceShippingInfo) < 2) {
                        $niceShippingInfo = $this->_tryGetExactMethod($methodTitle, $methodName);
                    }
                    break;

                case 'flatrate':
                    $niceShippingInfo = $this->_getConfigMethod('flatmethod');
                    if (count($niceShippingInfo) < 2) {
                        $niceShippingInfo = $this->_tryGetExactMethod($methodTitle, $methodName);
                    }
                    break;

                case 'tablerate':
                    $niceShippingInfo = $this->_tryGetExactMethod($methodTitle, $methodName);
                    break;

                case 'multipletablerates':
                    break;

                case 'matrixrate':
                    if ($_methodCode == 'free') {
                        $niceShippingInfo = $this->_getConfigMethod('freemethod');
                    }
                    break;
            }
        }

        if (count($niceShippingInfo) < 2) {
            $niceShippingInfo = $this->_tryParseShippingDescription($shippingDescription, $niceShippingInfo['carrier']);
        }

        return $niceShippingInfo;
    }

    protected function _tryParseShippingDescription($shippingDescription, $carrierCode = null) {

        $shippingMethod = explode(' - ', $shippingDescription);

        //USPS
        if(stristr($shippingDescription, 'United States Postal Service')
           || stristr($shippingDescription, 'USPS')) {
            $shippingMethod[0] = 'USPS';
            $shippingMethod[1] = (count($shippingMethod) > 1) ? str_replace('USPS ', '', $shippingMethod[1]) : 'N/A';
        } //UPS
        elseif(stristr($shippingDescription, 'United Parcel Service')
               || stristr($shippingDescription, 'UPS')) {
            $shippingMethod[0] = 'UPS';
            $shippingMethod[1] = (count($shippingMethod) > 1) ? str_replace('UPS ', '', $shippingMethod[1]) : '';
            if($shippingMethod[1] == 'Second Day Air') $shippingMethod[1] = '2nd Day Air';
            if($shippingMethod[1] == 'Next Day Air') $shippingMethod[1] = 'Next Day';
            if($shippingMethod[1] == 'Three-Day Select') $shippingMethod[1] = '3-Day Select';
            if($shippingMethod[1] == 'Next Day Air Early A.M.') $shippingMethod[1] = 'Next Day Air Early AM';
        } //FedEx
        elseif(stristr($shippingDescription, 'Federal Express')
               || stristr($shippingDescription, 'FedEx')
               || stristr($shippingDescription, 'Express Shipping')) {
            $shippingMethod[0] = 'FedEx';
            $shippingMethod[1] = (count($shippingMethod) > 1) ? str_replace('FedEx ', '', $shippingMethod[1]) : '';
            if($shippingMethod[1] == '2Day') $shippingMethod[1] = '2 Day';
            if($shippingMethod[1] == 'Home Delivery') $shippingMethod[1] = 'Ground Home';
            if($shippingMethod[1] == 'Intl. Economy') $shippingMethod[1] = 'International Economy';
        }

        if(is_array($shippingMethod)) {
            $shippingMethod[0] = (count($shippingMethod) > 0) ? $shippingMethod[0] : '';
            $shippingMethod[1] = (count($shippingMethod) > 1) ? $shippingMethod[1] : '';
        } else {
            $shippingMethod = array('N/A', $shippingDescription);
        }

        return array(
            'carrier' => $shippingMethod[0],
            'method' => $shippingMethod[1]
        );
    }

    protected function _tryGetExactMethod($carrier, $method = null) {
        $niceShippingInfo = array();
        $carrier = strtolower($carrier);
        switch($carrier) {
            case 'fedex':
            case 'fedexsoap':
                $niceShippingInfo['carrier'] = 'FedEx';
                if(isset($method)) {
                    if(isset($this->_fedexMethods[$method])) {
                        $niceShippingInfo['method'] = $this->_fedexMethods[$method];
                    } elseif(in_array($method, $this->_fedexMethods)) {
                        $niceShippingInfo['method'] = $method;
                    }
                }
                break;
            case 'ups':
                $niceShippingInfo['carrier'] = 'UPS';
                if(isset($method)) {
                    if(isset($this->_upsMethods[$method])) {
                        $niceShippingInfo['method'] = $this->_upsMethods[$method];
                    } elseif(in_array($method, $this->_upsMethods)) {
                        $niceShippingInfo['method'] = $method;
                    }
                }
                break;
            case 'usps':
                $niceShippingInfo['carrier'] = 'USPS';
                if(isset($method)) {
                    if(isset($this->_uspsMethods[$method])) {
                        $niceShippingInfo['method'] = $this->_uspsMethods[$method];
                    } elseif(in_array($method, $this->_uspsMethods)) {
                        $niceShippingInfo['method'] = $method;
                    }
                }
                break;
        }
        return $niceShippingInfo;
    }

    protected function _getConfigMethod($key) {
        $_config = $this->getShippingConfig();
        $niceShippingInfo = array();
        if ($_config[$key]) {
            $method = explode(' - ', $_config[$key]);
            $niceShippingInfo = $this->_tryGetExactMethod($method[0], $method[1]);
        }
        return $niceShippingInfo;
    }

    public function getConfigExportFrom() {
        $_config = $this->getGeneralConfig();
        $value = null;
        if ($_config['exportfrom']) {
            $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $value = Mage::app()->getLocale()->date($_config['exportfrom'], $format, null, false)->toString('Y-MM-dd 00:00:00');
        }
        return $value;
    }

    public function getGeneralConfig($key = null, $storeId = null) {
        
        //echo '<pre>CALLING GENERAL CONFIG : '.$key.' FOR STORE : '.$storeId;
        
        $_config = Mage::getStoreConfig($this->_generalConfigPath, $storeId);
        //echo print_r($_config, true).'</pre>';
        
        if($key === null)
            return $_config;
        else
            return $_config[$key];
    }

    public function getShippingConfig($key = null) {
        $_config = Mage::getStoreConfig($this->_shippingConfigPath);
        if($key === null)
            return $_config;
        else
            return $_config[$key];
    }

    public function getAuthConfig($key = null, $storeId = null) {
        $_config = Mage::getStoreConfig($this->_authConfigPath, $storeId);
        if($key === null)
            return $_config;
        else
            return $_config[$key];
    }
/*    
    public function getEmailConfig($key = null) {
        $_config = Mage::getStoreConfig($this->_emailConfigPath);
        if($key === null)
            return $_config;
        else
            return $_config[$key];
    }
*/
    public function canExportSku($sku, $storeId = null) {
        $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        if (!$_product) {
            return false;
        }
        
        $_isDisabled = $_product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED;
        $productInventorySettings = Mage::getModel('ordersync/inventory')->load($_product->getId(), 'product_id');
        
        //checking for overriden settings
        if ($productInventorySettings->getId()) {
            if(!$productInventorySettings->getAllowExportDefault()) {
                return (bool)$productInventorySettings->getAllowExport();
            }
        }

        //no overriden settings, using global
        $_allowExportDisabled = $this->getGeneralConfig('exportdisabled', $storeId);
        
        if (!$_allowExportDisabled && $_isDisabled) {
            return false;
        }
        
        return (bool)$this->getGeneralConfig('exportskus', $storeId);
    }

    public function canUpdateStock($sku) {
        $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        if (!$_product || $_product->isComposite()) {
            return false;
        }

        $_isDisabled = $_product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED;
        $productInventorySettings = Mage::getModel('ordersync/inventory')->load($_product->getId(), 'product_id');

        //checking for overriden settings
        if ($productInventorySettings->getId()) {
            if(!$productInventorySettings->getAllowStockUpdateDefault()) {
                return (bool)$productInventorySettings->getAllowStockUpdate();
            }
        }
        
        //no overriden settings, using global
        $_allowUpdateDisabled = $this->getGeneralConfig('updatedisabled');
        if (!$_allowUpdateDisabled && $_isDisabled) {
            return false;
        }
        
        return (bool)$this->getGeneralConfig('updateskus');
    }
}