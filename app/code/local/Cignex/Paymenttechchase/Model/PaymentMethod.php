  <?php
  error_reporting(0);

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
 * @category    Cignex
 * @package     Cignex_Paymentchase
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


  class Cignex_Paymenttechchase_Model_PaymentMethod extends Mage_Payment_Model_Method_Cc
  {
    const REQUEST_TYPE_AUTH_CAPTURE = 'AC';
    const REQUEST_TYPE_AUTH_ONLY    = 'A';
    const REQUEST_TYPE_CAPTURE_ONLY = 'C';
    const REQUEST_TYPE_CREDIT       = 'R';

    const RESPONSE_CODE_APPROVED = 1;
    const RESPONSE_CODE_DECLINED = 2;
    const RESPONSE_CODE_ERROR    = 3;
    const RESPONSE_CODE_HELD     = 4;

    const RESPONSE_PROC_STATUS_APPROVED = 0;
    
    protected $_code  = 'paymenttechchase';
    
    /**
     * Availability options
     */

    protected $_isGateway               = true;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;

   /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('paymenttechchase/chasepaymentech_result');
    }
    /**
     * Get api url of Orbital CHASE Paymentech
     *
     * @return string
     */
    public function getApiGatewayUrl()
    {
        $value = Mage::getStoreConfig('payment/paymenttechchase/cgi_url');
        return $value;
    }

    /**
     * Send authorize request to gateway
     *
     * @param  Varien_Object $payment
     * @param  decimal $amount
     * @return Cignex_Paymenttechchase_Model_PaymentMethod
     * @throws Mage_Core_Exception
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('paymenttechchase')->__('Invalid amount.'));
        }
        $payment->setChaseTransType(self::REQUEST_TYPE_AUTH_ONLY);
        $payment->setAmount($amount);

        $request = $this->_buildRequest($payment);
        $response = $this->_postRequest($request);
        if ($response) {
             $payment->setStatus(self::STATUS_APPROVED)
                  ->setLastTransId($this->getTransactionId());
        }else {
              $e = $this->getError();
              if (isset($e['message'])) {
                  $message = Mage::helper('paymenttechchase')->__('There has been an error processing your payment.') . $e['message'];
              }
          }
         return $this;
    }

    /**
     * Send capture request to gateway
     *
     * @param Varien_Object $payment
     * @param decimal $amount
     * @return Cignex_Paymenttechchase_Model_PaymentMethod
     * @throws Mage_Core_Exception
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setChaseTransType(self::REQUEST_TYPE_AUTH_CAPTURE);
        $payment->setAmount($amount);
        $this->setAmount($amount)
            ->setPayment($payment);
        $request = $this->_buildRequest($payment);
        $response = $this->_postRequest($request);
        if ($response) {
            $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId());
        } 
        else {
            $e = $this->getError();
            if (isset($e['message'])) {
                $message = Mage::helper('paymenttechchase')->__('There has been an error processing your payment.') . $e['message'];
            }
        }
        return $this;
    }

    /**
     * Send refund request to gateway
     *
     * @param Varien_Object $payment
     * @param decimal $amount
     * @return Cignex_Paymenttechchase_Model_PaymentMethod
     * @throws Mage_Core_Exception
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if ($payment->getLastTransId() && $amount > 0) {
            $payment->setChaseTransType(self::REQUEST_TYPE_CREDIT);
            $request = $this->_buildRequest($payment);
            $request->txRefNum = $payment->getLastTransId();
            $response = $this->_postRequest($request);
            if ($response) {
              $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId());
            } 
            else {
              $e = $this->getError();
              if (isset($e['message'])) {
                  $message = Mage::helper('paymenttechchase')->__('There has been an error processing your payment.') . $e['message'];
              }
          }
          return $this;
        }
    }
    
    /**
     * Cancel the transaction
     * @param Varien_Object $payment
     * @return Cignex_Paymenttechchase_Model_PaymentMethod
     */

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED);
        return $this;
    }
    
    /**
     * Prepare request to gateway
     *
     * @param Varien_Object $payment
     * @return NewOrderRequestElement $request
     */
    protected function _buildRequest(Varien_Object $payment)
    {
        $order = $payment->getOrder();
        $this->setStore($order->getStoreId());

        $request = new Cignex_Paymenttechchase_Model_NewOrderRequestElement;

       if ($order && $order->getIncrementId()) {
            $request->orderID = $order->getIncrementId();
            $billing = $payment->getOrder()->getBillingAddress();
            $request->avsZip = $billing->getPostcode();
        }
        if(strlen($payment->getCcExpMonth())==1)
          $exp=$payment->getCcExpYear()."0".$payment->getCcExpMonth();
        else
          $exp=$payment->getCcExpYear().$payment->getCcExpMonth();
        $request->orbitalConnectionUsername = $this->getConfigData('user_id');
        $request->orbitalConnectionPassword = $this->getConfigData('password');
        $request->bin = $this->getConfigData('bin_no');
        $request->merchantID = $this->getConfigData('merchant_id');
        $request->terminalID = $this->getConfigData('terminal_id');
        $request->transType = $payment->getChaseTransType(); 
        
        if(!$this->getConfigData('profile'))
        {
          $request->ccExp = $exp;
          if($payment->getCcCid() != '')
          	$request->ccCardVerifyNum = $payment->getCcCid();
          $request->ccAccountNum = $payment->getCcNumber();
          if(($payment->getCcType() == 'VI' || $payment->getCcType() == 'DI') &&  $payment->getCcCid() != '')
          	$request->ccCardVerifyPresenceInd = 1;
          $request->avsName = $payment->getCcOwner();
        }else{
          $CustRefNo = $this->_profileRequest($payment);
          $request->customerRefNum = $CustRefNo;
          $request->profileOrderOverideInd = 'NO';
          $request->useCustomerRefNum = $CustRefNo;
        }
        $request->comments =  "Test Web Service Authorize and Capture Transaction";
        $request->orderID = $payment->getOrder()->getIncrementId();
        $request->industryType = "EC";
        $request->addProfileFromOrder = '';
        
        
        if ($payment->getLastTransId() && $this->getAmount()>0) {
            $transId = $payment->getLastTransId();
        }
        if($this->getAmount()){
            $request->amount = str_replace('.','',$this->getAmount());
        }
        switch ($payment->getChaseTransType()) {
            case self::REQUEST_TYPE_CREDIT:
                $request->txRefNum = $transId;
                break;
        }
        return $request;
    }
    
     /**
     * Post request to gateway
     *
     * @param NewOrderRequestElement $return
     * @return boolean
     */

    protected function _postRequest(Cignex_Paymenttechchase_Model_NewOrderRequestElement $request)
    {
        $url = $this->getApiGatewayUrl().'/wsdl/PaymentechGateway.wsdl';
        $no = new Cignex_Paymenttechchase_Model_NewOrder;
        $no->newOrderRequest = $request;
        try{
           $client = new SoapClient( $url );
        }
        catch (Exception $e){
            Mage::log("\n".__FILE__." (".__LINE__.")\n".__METHOD__."\n Exception invoking SoapClient : ".$e->getMessage()." \n" );
            Mage::throwException($this->parseErrMessage($e->getMessage()));
        }
        try{
         
          $ret = $client->newOrder($no);
          $response = $ret->return;
        }
        catch (Exception $e){
          Mage::throwException($this->parseErrMessage($e->getMessage()));
        }
        if ($response->procStatus == self::RESPONSE_PROC_STATUS_APPROVED)
        {
           if($response->approvalStatus == self::RESPONSE_CODE_APPROVED)
           {
              $this->unsError();
           }
           else
           {
              if($response->procStatus != RESPONSE_PROC_STATUS_APPROVED)
              {
                $message = 'System Error : '.$response->statusMsg;
                $message = str_replace(' ','',$message);
                Mage::throwException($response->procStatusMessage);
              }
              else if($response->cvvRespCode!='M')
              {
                $message ='Cardholder Verification : '.$response->respMsg;
                $message = str_replace(' ','',$message);
                Mage::throwException($response->procStatusMessage);
              }
              else if($response->avsRespCode !='Z' || $response->avsRespCode !='9' || $response->avsRespCode !='H')
              {
                $message ='Address Verfication : '.$response->respMsg;
                $message = str_replace(' ','',$message);
                Mage::throwException($response->procStatusMessage);
              }
              else if($response->approvalStatus == 0 || $response->approvalStatus == 2)
              {
                $message = 'Card is in Decline State';
                Mage::throwException($response->procStatusMessage);
              }
              return false;
           } 
        }
        else
        {
          if($response->procStatus != RESPONSE_PROC_STATUS_APPROVED)
          {
            $message = 'System Error : '.$response->statusMsg;
            $message = str_replace(' ','',$message);
            Mage::throwException($response->procStatusMessage);
          }
          else if($response->cvvRespCode!='M')
          {
            $message ='Cardholder Verification : '.$response->respMsg;
            $message = str_replace(' ','',$message);
            Mage::throwException($response->procStatusMessage);
          }
          else if($response->avsRespCode !='Z' || $response->avsRespCode !='9' || $response->avsRespCode !='H')
          {
            $message ='Address Verfication : '.$response->respMsg;
            $message = str_replace(' ','',$message);
            Mage::throwException($response->procStatusMessage);
          }
          else if($response->approvalStatus == 0 || $response->approvalStatus == 2)
          {
            $message = 'Card is in Decline State';
            Mage::throwException($response->procStatusMessage);
          }
          return false;
       }
       $this->setTransactionId($response->txRefNum);
       return true;
    }
    /**
     * Prepare request to gateway
     *
     * @param Varien_Object $payment
     * @return variable $custRefNum
     */
    protected function _profileRequest(Varien_Object $payment)
    {
        $profilename = Mage::helper('paymenttechchase')->getProfileName();
        $session = Mage::getSingleton('customer/session');
        $custref = $session->getId();
        $CustRefNo = Mage::getResourceModel('paymenttechchase/profilemgmt')->getCustomerRefNumber($custref,$profilename);
        if(empty($CustRefNo))
        {
          $billingIds = $session->getCustomer()->getPrimaryAddressIds();
          $billingAddressId = '';
          if(!empty($billingIds))
          {
            $billingAddressId = $billingIds[0];
          }
          $profileArray = array();
          $profileArray['profilename'] = $profilename;
          $profileArray['firstname'] = $payment->getCcOwner();
          $profileArray['ccsave_expiration_yr'] = $payment->getCcExpYear();
          $profileArray['ccsave_expiration'] = $payment->getCcExpMonth();
          $billing = $payment->getOrder()->getBillingAddress();
          $profileArray['address1'] = $billing->getStreet1();
          $profileArray['address2'] = $billing->getStreet2();
          $profileArray['city'] = $billing->getCity();
          $regionID = $billing->getRegionId();
          $regionModel = Mage::getModel('directory/region')->load($regionID,'');
          $profileArray['state'] = $regionModel->getData('code');
          $profileArray['zip']= $billing->getPostcode();
          $profileArray['telephone'] = str_replace("-", "", $billing->getTelephone());
          $profileArray['country'] = $billing->getCountry();
          $profileArray['chase_cc_number'] = $payment->getCcNumber();
          Mage::getModel('paymenttechchase/profilemgmt')->addProfile($profileArray);
          $CustRefNo = Mage::getResourceModel('paymenttechchase/profilemgmt')->getCustomerRefNumber($custref,$profilename);
      }
      return $CustRefNo;
    }
    /**
     * Parse Error Message
     *
     * @param array $message
     * @return string $allerrMessage
     */

    public function parseErrMessage($message)
    {
      $errMessage =  @explode(' ',trim($message));
      $allerrMessage = '';
      if(count($errMessage)>0)
      {
        for($i=1;$i<count($errMessage);$i++)
        {
          $allerrMessage .= $errMessage[$i]." ";
        }
        Mage::log($allerrMessage);
      }

      return $allerrMessage;
    }
  }

