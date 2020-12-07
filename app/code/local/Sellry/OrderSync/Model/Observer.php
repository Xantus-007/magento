<?php
/**
 * The Magento Developer
 * http://themagentodeveloper.com
 *
 * @category   Sellry
 * @package    Sellry_OrderSync
 * @version    0.1.3
 */

class Sellry_OrderSync_Model_Observer extends Varien_Object {

    static protected $_singletonFlag = false;
    
    var $_exportFrom = null;
    var $_isForce = false;
    var $_maxLogSize = 10485760; //10mb
    var $_currentStoreId;

    public function __construct() {
        parent::__construct();
        $this->_exportFrom = Mage::helper('ordersync')->getConfigExportFrom();
    }

    public function setExportFrom($fromDate) {
        if ($fromDate) {
            $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $value = Mage::app()->getLocale()->date($fromDate, $format, null, false)->toString('Y-MM-dd 00:00:00');
            $this->_exportFrom = $value;
        }
        return $this;
    }

    public function setIsForce($value) {
        $this->_isForce = $value;
        return $this;
    }

    //wrapper for logMessage in helper
    protected function _logMessage($message) {
        Mage::helper('ordersync')->logMessage($message);
    }

    protected function _orderExport($storeId) {
        $_helper = Mage::helper('ordersync');
        $this->_currentStoreId = $storeId;
        //checking if we can export
        $canExport = $_helper->getGeneralConfig('allowexport', $storeId);
        
	$h = Mage::helper('ordersync');

$h->logMessage('is allowed : '.$canExport);

	if(!$canExport) {
            return false;
        }
        
        $this->_logMessage('exporting orders ...');

        $_authConfig = $_helper->getAuthConfig(null, $storeId);
        $_upsConfig = Mage::getStoreConfig('carriers/ups');
        $_fedexConfig = Mage::getStoreConfig('carriers/fedex');

        $orders = Mage::getModel('sales/order')->getCollection();
        
        
        $orders->addFieldToFilter('state', array('eq'=>"processing"));
        $orders->addFieldToFilter('store_id', $storeId);
        
        if($this->_exportFrom) {
            $orders->addFieldToFilter('created_at', array('gt' => $this->_exportFrom));
        }
        $h->logMessage('ORDER QUERY : '.$orders->getSelect());
        $correctCount = 0;
        $errorCount = 0;
        foreach($orders as $order) {

            try {
                if ($order->getSyncStatus() == 'exported'
                    || $order->getSyncStatus() == 'got_tracking') {
                    continue;
                }

                $orderId = $order->getIncrementId();
                $this->_logMessage("exporting order_id={$orderId}");

                $shipping = $order->getShippingAddress();
                if (!$shipping) {
                    $this->_logMessage("shipping is null");
                    continue;
                }

                $shippingStreet = $shipping->getStreet();
                $shippingStreet[0] = (count($shippingStreet) > 0)? $shippingStreet[0] : '';
                $shippingStreet[1] = (count($shippingStreet) > 1)? $shippingStreet[1] : '';

                $billingOption = 'BILL THIRD PARTY';
                $shipperAccount = '';

                //processing shipping info
                $shippingMethod = $_helper->getNiceShippingMethod($order);
                switch($shippingMethod['carrier']) {
                    case 'UPS':
                        //@TODO : Set this in admin.
                        $shipperAccount = 'EA2320';                           
                        break;
                    case 'FedEx':
                        $shipperAccount = $_fedexConfig['account'];
                        $billingOption = 'Sender';
                        break;
                }

                $countryModel = Mage::getModel('directory/country')->loadByCode($shipping->getCountry());
                $data = "<?xml version='1.0' encoding='utf-8'?><soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
                         <soap:Body>
                            <extLoginData xmlns='http://www.JOI.com/schemas/ViaSub.WMS/'>
                              <ThreePLKey>{$_authConfig['key']}</ThreePLKey>
                              <Login>{$_authConfig['login']}</Login>
                              <Password>{$_authConfig['password']}</Password>
                              <FacilityID>{$_authConfig['fascilityid']}</FacilityID>
                            </extLoginData>
                            <orders xmlns='http://www.JOI.com/schemas/ViaSub.WMS/'>";

                //cleaning raw data
                $companyName = (preg_replace('/(\n|\r|\s|\t)/', '', $shipping->getCompany()) != '') ? $shipping->getCompany() : $shipping->getFirstname() . ' ' . $shipping->getLastname();

                $country = $this->_translateCountry(htmlentities($countryModel->getName()), 'en_US');
                $regionCode = htmlentities($shipping->getRegionCode());
                $city = htmlentities($shipping->getCity());
                $zipCode = htmlentities($shipping->getPostcode());
                $shippingStreet1 = htmlentities($shippingStreet[0]);
                $shippingStreet2 = htmlentities($shippingStreet[1]);
                $companyName = htmlentities($companyName);
                $phone = htmlentities($shipping->getTelephone());
                $email = htmlentities($shipping->getEmail());
                $customerName = htmlentities($shipping->getFirstname(). " " .$shipping->getLastname());

                $data .= "<Order>
                            <TransInfo>
                              <ReferenceNum>{$orderId}</ReferenceNum>
                            </TransInfo>
                            <ShipTo>
                              <Name>{$customerName}</Name>
                              <CompanyName>{$companyName}</CompanyName>
                              <Address>
                                    <Address1>{$shippingStreet1}</Address1>
                                    <Address2>{$shippingStreet2}</Address2>
                                    <City>{$city}</City>
                                    <State>{$regionCode}</State>
                                    <Zip>{$zipCode}</Zip>
                                    <Country>{$country}</Country>
                              </Address>
                              <PhoneNumber1>{$phone}</PhoneNumber1>
                              <EmailAddress1>{$email}</EmailAddress1>
                              <CustomerName>{$customerName}</CustomerName>
                            </ShipTo>
                            <ShippingInstructions>
                              <Carrier>{$shippingMethod['carrier']}</Carrier>
                              <Mode>{$shippingMethod['method']}</Mode>
                              <BillingCode>{$billingOption}</BillingCode>
                              <Account>{$shipperAccount}</Account>
                            </ShippingInstructions>
                            <OrderLineItems>";

                $orderItems = $order->getAllItems();
                
                $allItems = array();
                foreach($orderItems as $item) {
                    
                    $sku = $item->getSku();
                    if($item->getParentItemId() != null ||
                       !$_helper->canExportSku($sku, $storeId)) {
                        continue;
                    }

                    $qty = round($item->getQtyOrdered(), 2);
                    
                    if( ! isset($allItems[$sku]) ) {
                        $allItems[$sku] = $qty;
                    } else {
                        $allItems[$sku] += $qty;
                    }

                    $qty = $allItems[$item->getSku()];
                    
                    $data .= "
                                    <OrderLineItem>
                                        <SKU>{$sku}</SKU>
                                        <Qty>{$qty}</Qty>
                                    </OrderLineItem>";
                }

                $data .= "
                                </OrderLineItems>
                            </Order>
                        </orders>
                    </soap:Body>
                </soap:Envelope>";
                
                if (count($allItems) == 0) {
                    $this->_logMessage("no items to send in current order");
                    continue;
                }
                
                $reply = $this->_sendXmlToServer($data, "CreateOrders");
                if ($reply === false) {
                    $errorCount++;
                    $this->_logMessage("reply is not valid xml");
                    continue; //will try again on this order next time
                }

                if(!strpos($reply, 'faultcode')) {
                    $order->setSyncStatus('exported');
                    $order->setSyncInventoryStatus(serialize($allItems));
                    
                    $order->save();
                    $correctCount++;
                } else {
                    throw new Exception($reply);
                }
            } catch (Exception $e) {
                $errorCount++;
                $this->_logMessage("order export FAILED, order_id={$orderId}. Details: " . $e->getMessage());
            }
        }
        $this->_logMessage("EXPORTED: {$correctCount}, FAILED: {$errorCount}");
        return true;
    }
    
    protected function _translateCountry($country, $locale)
    {
        $startLocale = new Zend_Locale('fr_FR');
        $frCountries = $startLocale->getTranslationList('Territory', $startLocale->getLanguage(), 2);
        $frCountries = array_flip($frCountries);
        
        $countryCode = $frCountries[$country];
        
        $endLocale = new Zend_Locale($locale);
        $endCountries = $endLocale->getTranslationList('Territory', $endLocale->getLanguage(), 2);
        $result = $endCountries[$countryCode];
        
        if(strlen($result) == 0)
        {
            $result = $country;
        }
        
        return $result;
    }

    protected function _lastOrderExport($lastOrderId) {

        $_helper = Mage::helper('ordersync');
        
//        $this->_logMessage('exporting orders ...');

        $_authConfig = $_helper->getAuthConfig();

        $_upsConfig = Mage::getStoreConfig('carriers/ups');
        $_fedexConfig = Mage::getStoreConfig('carriers/fedex');

        $orders = Mage::getModel('sales/order')->getCollection();
        $orders->addFieldToFilter('entity_id', array('eq'=>$lastOrderId));
        $orders->getSelect()->limit(1);         

        foreach($orders as $order) {

            try {
                if ($order->getSyncStatus() == 'exported'
                    || $order->getSyncStatus() == 'got_tracking') {
                    continue;
                }

                $orderId = $order->getIncrementId();
//                $this->_logMessage("exporting order_id={$orderId}");

                $shipping = $order->getShippingAddress();
                if (!$shipping) {
//                    $this->_logMessage("shipping is null");
                    continue;
                }

                $shippingStreet = $shipping->getStreet();
                $shippingStreet[0] = (count($shippingStreet) > 0)? $shippingStreet[0] : '';
                $shippingStreet[1] = (count($shippingStreet) > 1)? $shippingStreet[1] : '';

                $billingOption = 'Prepaid';
                $shipperAccount = '';

                //processing shipping info
                $shippingMethod = $_helper->getNiceShippingMethod($order);
                switch($shippingMethod['carrier']) {
                    case 'UPS':
                        $shipperAccount = $_upsConfig['shipper_number'];
                        break;
                    case 'FedEx':
                        $shipperAccount = $_fedexConfig['account'];
                        $billingOption = 'Sender';
                        break;
                }

                $countryModel = Mage::getModel('directory/country')->loadByCode($shipping->getCountry());
                $data = "<?xml version='1.0' encoding='utf-8'?><soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
                         <soap:Body>
                            <extLoginData xmlns='http://www.JOI.com/schemas/ViaSub.WMS/'>
                              <ThreePLKey>{$_authConfig['key']}</ThreePLKey>
                              <Login>{$_authConfig['login']}</Login>
                              <Password>{$_authConfig['password']}</Password>
                              <FacilityID>{$_authConfig['fascilityid']}</FacilityID>
                            </extLoginData>
                            <orders xmlns='http://www.JOI.com/schemas/ViaSub.WMS/'>";

                //cleaning raw data
                $companyName = (preg_replace('/(\n|\r|\s|\t)/', '', $shipping->getCompany()) != '') ? $shipping->getCompany() : $shipping->getFirstname() . ' ' . $shipping->getLastname();

                $country = htmlentities($countryModel->getName());
                $regionCode = htmlentities($shipping->getRegionCode());
                $city = htmlentities($shipping->getCity());
                $zipCode = htmlentities($shipping->getPostcode());
                $shippingStreet1 = htmlentities($shippingStreet[0]);
                $shippingStreet2 = htmlentities($shippingStreet[1]);
                $companyName = htmlentities($companyName);
                $phone = htmlentities($shipping->getTelephone());
                $email = htmlentities($shipping->getEmail());
                $customerName = htmlentities($shipping->getFirstname(). " " .$shipping->getLastname());

                $data .= "<Order>
                            <TransInfo>
                              <ReferenceNum>{$orderId}</ReferenceNum>
                            </TransInfo>
                            <ShipTo>
                              <Name>{$customerName}</Name>
                              <CompanyName>{$companyName}</CompanyName>
                              <Address>
                                    <Address1>{$shippingStreet1}</Address1>
                                    <Address2>{$shippingStreet2}</Address2>
                                    <City>{$city}</City>
                                    <State>{$regionCode}</State>
                                    <Zip>{$zipCode}</Zip>
                                    <Country>{$country}</Country>
                              </Address>
                              <PhoneNumber1>{$phone}</PhoneNumber1>
                              <EmailAddress1>{$email}</EmailAddress1>
                              <CustomerName>{$customerName}</CustomerName>
                            </ShipTo>
                            <ShippingInstructions>
                              <Carrier>{$shippingMethod['carrier']}</Carrier>
                              <Mode>{$shippingMethod['method']}</Mode>
                              <BillingCode>{$billingOption}</BillingCode>
                              <Account>{$shipperAccount}</Account>
                            </ShippingInstructions>
                            <OrderLineItems>";

                $orderItems = $order->getAllItems();
                
                $allItems = array();
                foreach($orderItems as $item) {
                    
                    $sku = $item->getSku();
                    if($item->getParentItemId() != null
                       || !$_helper->canExportSku($sku)) {
                        
                        continue;
                    }

                    $qty = round($item->getQtyOrdered(), 2);
                    
                    if( ! isset($allItems[$sku]) ) {
                        $allItems[$sku] = $qty;
                    } else {
                        $allItems[$sku] += $qty;
                    }

                    $qty = $allItems[$item->getSku()];
                    
                    $data .= "
                                    <OrderLineItem>
                                        <SKU>{$sku}</SKU>
                                        <Qty>{$qty}</Qty>
                                    </OrderLineItem>";
                }

                $data .= "
                                </OrderLineItems>
                            </Order>
                        </orders>
                    </soap:Body>
                </soap:Envelope>";
                
                if (count($allItems) == 0) {
//                    $this->_logMessage("no items to send in current order");
                    continue;
                }

                $reply = $this->_sendXmlToServer($data, "CreateOrders");
                if ($reply === false) {
                    $errorCount++;
//                    $this->_logMessage("reply is not valid xml");
                    continue; //will try again on this order next time
                }

                
                if(!strpos($reply, 'faultcode')) {
                    $order->setSyncStatus('exported');
                    $order->setSyncInventoryStatus(serialize($allItems));
                    
                    $order->save();
                    $correctCount++;
                } else {
                    throw new Exception($reply);
                }
            } catch (Exception $e) {
                $errorCount++;
//                $this->_logMessage("order export FAILED, order_id={$orderId}. Details: " . $e->getMessage());
            }
        }
//        $this->_logMessage("EXPORTED: {$correctCount}, FAILED: {$errorCount}");
        return true;
    }

    protected function _trackingImport($storeId) {
        
        $this->_currentStoreId = $storeId;
        $_helper = Mage::helper('ordersync');

        //checking if we can import
        $canImport = $_helper->getGeneralConfig('allowimport', $storeId);
        
        //$_emailConfig = $_helper->getEmailConfig();
        if(!$canImport) {
            return false;
        }

        $this->_logMessage('importing tracking info ...');
        $_authConfig = $_helper->getAuthConfig(null, $storeId);
        
        $orders = Mage::getModel('sales/order')->getCollection();
        $orders->addFieldToFilter('sync_status', array('neq'=>"got_tracking"));
        $orders->addFieldToFilter('state', array('eq'=>"processing"));
        $orders->addFieldToFilter('store_id', $storeId);
        
        //@todo: check if we still need date restrictions here
        if($this->_exportFrom) {
            $orders->addFieldToFilter('created_at', array('gt' => $this->_exportFrom));
        }

        $correctCount = 0;
        $errorCount = 0;
        foreach($orders as $order) {
            try {
                $orderId = $order->getIncrementId();
                $data = "<?xml version='1.0' encoding='utf-8'?>
                         <soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
                            <soap:Body>
                                <userLoginData xmlns='http://www.JOI.com/schemas/ViaSub.WMS/'>
                                    <ThreePLID>{$_authConfig['threeplid']}</ThreePLID>
                                    <Login>{$_authConfig['login']}</Login>
                                    <Password>{$_authConfig['password']}</Password>
                                </userLoginData>
                                <focr xmlns='http://www.JOI.com/schemas/ViaSub.WMS/'>
                                    <CustomerID>{$_authConfig['custid']}</CustomerID>
                                    <FacilityID>{$_authConfig['fascilityid']}</FacilityID>
                                    <OverAlloc>Any</OverAlloc>
                                    <Closed>Any</Closed>
                                    <ASNSent>Any</ASNSent>
                                    <RouteSent>Any</RouteSent>
                                    <ReferenceNum>{$orderId}</ReferenceNum>
                                </focr>
                            </soap:Body>
                        </soap:Envelope>";

                $reply = $this->_sendXmlToServer($data, "FindOrders");
                $reply = html_entity_decode($reply);
                $xml = simplexml_load_string($reply);
                if ($xml === false) {
                    $errorCount++;
                    $this->_logMessage("reply is not valid xml");
                    continue;
                }
                
                $xml->registerXPathNamespace('fo', 'http://www.JOI.com/schemas/ViaSub.WMS/');
                $replyObject = $xml->xpath('//fo:orders');
                $replyObject = $replyObject[0];
                
                if(count($replyObject) > 0) {
                    $shipDate = (string)$replyObject->order->EarliestShipDate;
                    $trackingNum = (string)$replyObject->order->TrackingNumber;
                    $carrierTitle = (string)$replyObject->order->Carrier .' - '. (string)$replyObject->order->ShipMethod;
                    $carrier = strtolower((string)$replyObject->order->Carrier);
                    
                    //////
                    //$trackingNum = 'T3ST745678909876543GG';
                    /////

                    $shippingAddr = $order->getShippingAddress();
                    $email = $shippingAddr->getEmail();
                 
                    if(strlen($trackingNum) > 0) {
                        if ($this->_updateOrder($orderId, $email, $carrier, $carrierTitle, $trackingNum, $storeId)) {
                            $correctCount++;
                            $this->_logMessage("got tracking for order_id={$orderId} : {$trackingNum}");
                            
/*/sending email                          

                            $_emailConfig['emailtemplate'] = str_replace('%order%',$orderId,$_emailConfig['emailtemplate']);
                            $_emailConfig['emailtemplate'] = str_replace('%tracking%',$trackingNum,$_emailConfig['emailtemplate']);
                            $_emailConfig['emailtemplate'] = nl2br($_emailConfig['emailtemplate']);
                            
                            $mail = Mage::getModel('core/email');
                            $mail->setToName((string)$replyObject->order->ShipToName);
                            $mail->setToEmail($email);
                            $mail->setBody($_emailConfig['emailtemplate']);
                            $mail->setSubject($_emailConfig['emailsubject']);
                            $mail->setFromEmail($_emailConfig['emailadm']);
                            $mail->setFromName($_emailConfig['emailadm']);
                            $mail->setType('html');

                            try {
                            $mail->send();
                            }
                            catch (Exception $e) {
                            }                            
//end sending email  */ 
                        }
                    }
                }
            } catch (Exception $e) {
                $errorCount++;
                $this->_logMessage("order update FAILED, order_id={$orderId}. Details: " . $e->getMessage());
            }
        }
        $this->_logMessage("UPDATED: {$correctCount}, FAILED: {$errorCount}");
        return true;
    }

    protected function _updateOrder($orderId, $email, $carrier, $carrierTitle, $trackingNum, $storeId = null) {

        $includeComment = true;
        $comment = "shipped via {$carrierTitle} with Tracking Number: {$trackingNum}";

        //checking if we can send email
        $canSendEmail = (bool)Mage::helper('ordersync')->getGeneralConfig('sendemail', $storeId) && (bool)$email;

        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $convertOrder = Mage::getModel('sales/convert_order');
        $shipment = $convertOrder->toShipment($order);

        $exportedSkus = array();
        if($hasInventorySyncStatus = strlen($order->getSyncInventoryStatus()) > 0) {
            $exportedSkus = unserialize($order->getSyncInventoryStatus());
        }

        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getQtyToShip()) {
                continue;
            }
            if ($orderItem->getIsVirtual()) {
                continue;
            }

            //if we have data about exported skus - ship only that items, otherwise ship all
            if ($hasInventorySyncStatus) {
                if (!isset($exportedSkus[$orderItem->getSku()])) {
                    continue;
                }
            }

            $item = $convertOrder->itemToShipmentItem($orderItem);
            $qty = $orderItem->getQtyToShip();

            $item->setQty($qty);
            $shipment->addItem($item);
        }

        $data = array();
        $data['carrier_code'] = $carrier;
        $data['title'] = $carrierTitle;
        $data['number'] = $trackingNum;

        $track = Mage::getModel('sales/order_shipment_track')->addData($data);
        $shipment->addTrack($track);

        $shipment->register();
        $shipment->addComment($comment, false);
        $shipment->setEmailSent($canSendEmail);
        $shipment->getOrder()->setIsInProcess(true);

        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();

        if($canSendEmail) {
            $shipment->sendEmail($email, ($includeComment ? $comment : ''));
        }
        $shipment->save();

        //updating sync status
        $order->setSyncStatus('got_tracking');
        $order->save();

        return true;
    }

    protected function _getStockLevels() {

        $_authConfig = Mage::helper('ordersync')->getAuthConfig();
        
        $data = "<?xml version='1.0' encoding='utf-8'?><soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
                    <soap:Body>
                    <userLoginData xmlns='http://www.JOI.com/schemas/ViaSub.WMS/'>
                        <ThreePLID>{$_authConfig['threeplid']}</ThreePLID>
                        <Login>{$_authConfig['login']}</Login>
                        <Password>{$_authConfig['password']}</Password>
                    </userLoginData>
                    </soap:Body>
                </soap:Envelope>";

        try {
            $reply = $this->_sendXmlToServer($data, "ReportStockStatus");
            if ($reply === false) {
                throw new Exception("no reply");
            }

            if(strpos($reply, 'faultcode')) {
                throw new Exception('error retrieving stock data');
            }

            //processing reply
            $reply = html_entity_decode($reply);
            $xml = simplexml_load_string($reply);
            if ($xml === false) {
                throw new Exception("reply is not valid xml");
            }
            $xml->registerXPathNamespace('sr', 'http://www.JOI.com/schemas/ViaSub.WMS/');
            $stockData = $xml->xpath('//sr:Q');
            if(count($stockData) == 0) {
                throw new Exception("no stock data");
            }
        } catch (Exception $e) {
            $this->_logMessage("{$e->getMessage()}: {$reply}");
        }

        //processing skus with multiple locations
        $preparedStockData = array();
        foreach($stockData as $item) {
            $itemSku =  $item->SKU->asXML();
            preg_match('/>([^~]+)</u', $itemSku, $itemSku111);
            $itemQty =  $item->SUMOFAVAILABLE->asXML();
            preg_match('/>([^~]+)</u', $itemQty, $itemQty111);            

            $itemSku = trim($itemSku111[1]);
            $itemQty = (int)trim($itemQty111[1]);
            if (isset($preparedStockData[$itemSku])) {
                $preparedStockData[$itemSku] += $itemQty;
            } else {
                $preparedStockData[$itemSku] = $itemQty;
            }
        }

        return $preparedStockData;
    }

    protected function _updateStock() {

        $_helper = Mage::helper('ordersync');

        //checking if we can update stock levels
        $canUpdateStock = $_helper->getGeneralConfig('allowstockupdate');
        if(!$canUpdateStock) {
            return false;
        }

        $this->_logMessage('updating stock levels...');

        $currentStore = Mage::app()->getStore()->getId();
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        $stockData = $this->_getStockLevels();

        //updating stock levels
        foreach($stockData as $sku => $qty) {
            if(!$_helper->canUpdateStock($sku)) {
                continue;
            }

            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
            if (!$product) {
                $this->_logMessage("{$sku} doesn't exist");
                continue;
            }

            $origQty = 0;

            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
            if (!$stockItem->getId()) {
                $stockItem->setData('product_id', $product->getId());
                $stockItem->setData('stock_id', 1);
            } else {
                $origQty = $stockItem->getQty();
            }
            $stockItem->setData('qty', $qty);
            $stockItem->setData('is_in_stock', $qty > 0);

            try {
                $stockItem->save();
                if ($origQty != $qty) {
                    $this->_logMessage("updated stock for {$sku} ({$qty})");
                }
            } catch (Exception $e) {
                $this->_logMessage("failed to update stock for {$sku}: {$e->getMessage()}");
                continue;
            }
        }

        $this->_logMessage('stock updated');

        Mage::app()->setCurrentStore($currentStore);
        return true;
    }

    protected function _sendXmlToServer($data, $action, $url=false) {
        if (!$url) {
            $url = "https://app02.3plcentral.com/WebServiceExternal/Contracts.asmx";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3000);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "SOAPAction: \"http://www.JOI.com/schemas/ViaSub.WMS/{$action}\"", 'Connection: close'));

        $result = curl_exec($ch);

        if(curl_error($ch)) {
            $this->_logMessage(curl_error($ch));
            return false;
        }

        curl_close($ch);
        return $result;
    }

    public function updateStock() {
        
        $stockUpdateResult = $this->_updateStock();
        $result = array('success' => true, 'msg' => 'Stock levels updated.');
        if(!$stockUpdateResult) {
            $result = array('success' => false, 'msg' => 'Stock Updates are disabled. Check Order Sync settings');
        }
        
        return $result;
    }
    
    public function updateLastOrder($lastOrderId) {
        
        $res = $this->_lastOrderExport($lastOrderId);
        return $res;
    }

    public function syncIt() {
        $this->_logRotate();
        
        $this->_logMessage('sync started');

        //Load stores and get config for each store
        $stores = Mage::getModel('core/store')->getCollection();
        $exportResult = array();
        $importResult = array();
        $message = array();
        
        foreach($stores as $store)
        {
            $this->_currentStoreId = $store->getId();
            
            $exportMessages[] = $this->_orderExport($store->getId()) ? 'Export success on store : '.$store->getName() : 'Export not enabled on store '.$store->getName();
            $importMessages[] = $this->_trackingImport($store->getId()) ? 'Import success on store : '.$store->getName() : 'Import error on store '.$store->getName();
        }
        
        $result = array('success' => true, 'msg' => 'Synchronization finished');
        $results = $exportMessages + $importMessages;
        
        $result['msg'] .= implode('<br />', $results);
        
        /*
        if(!$exportResult && !$importResult) {
            $result = array('success' => false, 'msg' => 'Synchronization failed. Order export/import is disabled. Check Order Sync settings');
        } elseif(!$exportResult) {
            $result = array('success' => true, 'msg' => 'Synchronization finished (import only). Check Order Sync settings to allow export');
        } elseif(!$importResult) {
            $result = array('success' => true, 'msg' => 'Synchronization finished (export only). Check Order Sync settings to allow import');
        }
         */

        $this->_logMessage('sync finished');
        return $result;
    }

    //handling custom inventory options save
    public function saveProductTabData(Varien_Event_Observer $observer) {
        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;

            $product = $observer->getEvent()->getProduct();
            try {
                $request =  Mage::app()->getRequest();
                $allowExport = $request->getParam('ordersync_inventory_allow_export');
                $allowExportDefault = $request->getParam('ordersync_inventory_allow_export_default');
                $allowStockUpdate = $request->getParam('ordersync_inventory_allow_stock_update');
                $allowStockUpdateDefault = $request->getParam('ordersync_inventory_allow_stock_update_default');

                if ($allowExportDefault === null 
                    && $allowStockUpdateDefault === null
                    && $allowExport === null
                    && $allowStockUpdate === null) {
                    return;
                }

                $productInventorySettings = Mage::getModel('ordersync/inventory')->load($product->getId(), 'product_id');
                $productInventorySettings
                    ->setProductId($product->getId())
                    ->setAllowExport((bool)$allowExport)
                    ->setAllowExportDefault((bool)$allowExportDefault)
                    ->setAllowStockUpdate((bool)$allowStockUpdate)
                    ->setAllowStockUpdateDefault((bool)$allowStockUpdateDefault)
                    ->save();
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }
    
    protected function _logRotate() {
        $_helper = Mage::helper('ordersync');
        $file = $_helper->getLogLocation() . $_helper->getLogFilename();
        if (file_exists($file) && filesize($file) >= $this->_maxLogSize) {
            $newFile = $_helper->getLogLocation() . "ordersync_" . gmdate('Y-m-d_H-i-s') . ".log";
            rename($file, $newFile);
        }
    }
}
