  <?php
  /**
  * Magento Community Edition
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

  class Cignex_Paymenttechchase_Model_Profilemgmt extends Mage_Core_Model_Abstract
  {
    public function _construct()
    {
        parent::_construct();
        $this->_init('paymenttechchase/profilemgmt');
    }

    /**
     * Get api url of Orbital CHASE Paymentech
     *
     * @return string
     */
    public function getApiGatewayUrl()
    {
        $value = Mage::getStoreConfig('payment/paymenttechchase/cgi_url')."/wsdl/PaymentechGateway.wsdl";
        return $value;
    }

    /**
     * Fetch profile details from Paymentchase
     * @Param $custRefNumber => Referance number of profile in paymentchase
     */
    public function fetchProfile($custRefNumber)
    {
      $pr = new Cignex_Paymenttechchase_Model_ProfileFetch(); // Profile  Fetch Object
      $preq = new Cignex_Paymenttechchase_Model_ProfileFetchElement(); // Profile request element
      $preq->orbitalConnectionUsername = Mage::getStoreConfig('payment/paymenttechchase/user_id');
      $preq->orbitalConnectionPassword = Mage::getStoreConfig('payment/paymenttechchase/password');
      $preq->version="1.9";
      $preq->bin = Mage::getStoreConfig('payment/paymenttechchase/bin_no');
      $preq->merchantID = Mage::getStoreConfig('payment/paymenttechchase/merchant_id');
    
      $CustData = Mage::getSingleton('customer/session')->getCustomer();
      $CustId = $CustData->getId(); 
      $preq->customerName = $CustData->getName();
      $preq->customerRefNum = $custRefNumber;
      
      $pr->profileFetchRequest = $preq;

      try{
        $soap = new SoapClient($this->getApiGatewayUrl());
      }
      catch (Exception $e){
        Mage::log("\n".__FILE__." (".__LINE__.")\n".__METHOD__."\n Exception invoking SoapClient : ".$e->getMessage()." \n" );
        Mage::throwException($this->parseErrMessage($e->getMessage()));
      }
      try{
        $ret = $soap->ProfileFetch($pr);
        $response = $ret->return;
        return $response;
      }
      catch (Exception $e){
        Mage::throwException($this->parseErrMessage($e->getMessage()));
      }
  }

    /**
     * Add profile details from Paymentchase
     * @Param $request => Array of profile details
     */
    public function addProfile($request)
    {
      $pra = new Cignex_Paymenttechchase_Model_ProfileAdd();
      $pareq = new Cignex_Paymenttechchase_Model_ProfileAddElement();

      $pareq->orbitalConnectionUsername = Mage::getStoreConfig('payment/paymenttechchase/user_id');
      $pareq->orbitalConnectionPassword = Mage::getStoreConfig('payment/paymenttechchase/password');
      $pareq->version="1.9";
      $pareq->bin = Mage::getStoreConfig('payment/paymenttechchase/bin_no');
      $pareq->merchantID = Mage::getStoreConfig('payment/paymenttechchase/merchant_id');

      $CustData = Mage::getSingleton('customer/session')->getCustomer();

      $pareq->customerName = $request['firstname'];
      $pareq->customerProfileOrderOverideInd= 'NO';
      $pareq->customerProfileFromOrderInd= 'S';
      $pareq->profileOrderOverideInd = 'Yes';
      $RefNumber = $this->GenerateRefNumber();
      $pareq->customerRefNum = $RefNumber;
      $pareq->customerAccountType = 'CC';
      $pareq->ccAccountNum = $request['chase_cc_number'];

      if(strlen($request['ccsave_expiration'])==1)
        $Expdate=$request['ccsave_expiration_yr']."0".$request['ccsave_expiration'];
      else
        $Expdate=$request['ccsave_expiration_yr'].$request['ccsave_expiration'];

      $pareq->ccExp = $Expdate;
      $pareq->customerAddress1 = $request['address1'];
      $pareq->customerAddress2 = $request['address2'];
      $pareq->customerCity = $request['city'];
      $pareq->customerState = $request['state'];
      $pareq->customerZIP = $request['zip'];
      $pareq->customerPhone = str_replace("-", "", $request['telephone']);
      $pareq->customerCountryCode = $request['country'];
      $custref = $CustData->getId();
      $pra->profileAddRequest = $pareq;

      try
      {
        $soap = new SoapClient($this->getApiGatewayUrl());
      }
      catch (exception $e){
        Mage::throwException($this->parseErrMessage($e->getMessage()));
      }
      try
      {
        $ret = $soap->ProfileAdd($pra);
      }
      catch (exception $e){
        Mage::throwException($this->parseErrMessage($e->getMessage()));
        }
      try
      {
        $PrcId = $RefNumber;
        Mage::getResourceModel('paymenttechchase/profilemgmt')->addProfileName($custref,$PrcId,$request['profilename']);
        $response = $ret->return;
      }
      catch(exception $e)
      {
        Mage::throwException($this->parseErrMessage($e->getMessage()));
      }
    }

    /**
     * Delete profile from Paymentchase
     * @Param $custRefNum => Referance number of profile in paymentchase
     */
    public function deleteProfile($custRefNum)
    {
      $prd = new Cignex_Paymenttechchase_Model_ProfileDelete();
      $pdreq = new Cignex_Paymenttechchase_Model_ProfileDeleteElement();
      $pdreq->orbitalConnectionUsername = Mage::getStoreConfig('payment/paymenttechchase/user_id');
      $pdreq->orbitalConnectionPassword = Mage::getStoreConfig('payment/paymenttechchase/password');
      $pdreq->version="1.9";
      $pdreq->bin = Mage::getStoreConfig('payment/paymenttechchase/bin_no');
      $pdreq->merchantID = Mage::getStoreConfig('payment/paymenttechchase/merchant_id');
      $CustData = Mage::getSingleton('customer/session')->getCustomer();
      $CustArray =$CustData->getData();
      $pdreq->customerRefNum = $custRefNum;
      $prd->profileDeleteRequest = $pdreq;
      $soap = new SoapClient($this->getApiGatewayUrl());
      $ret = $soap->ProfileDelete($prd);
      try{
        Mage::getResourceModel('paymenttechchase/profilemgmt')->deleteProfileName($custRefNum);
        $response = $ret->return;
      }
      catch(exception $e)
      {
        Mage::throwException($this->parseErrMessage($e->getMessage()));
      }
      return;
    }

    /**
     * Edit profile details from Paymentchase - Should not pass profilename as it should not be editable
     * @Param $request => Array of profile details including Referance number
     */
    public function editProfile($request)
    {
      $pre = new Cignex_Paymenttechchase_Model_ProfileChange();
      $pereq = new Cignex_Paymenttechchase_Model_ProfileChangeElement();
      $pereq->orbitalConnectionUsername = Mage::getStoreConfig('payment/paymenttechchase/user_id');
      $pereq->orbitalConnectionPassword = Mage::getStoreConfig('payment/paymenttechchase/password');
      $pereq->version="1.9";
      $pereq->bin = Mage::getStoreConfig('payment/paymenttechchase/bin_no');
      $pereq->merchantID = Mage::getStoreConfig('payment/paymenttechchase/merchant_id');
      $pereq->customerName = $request['firstname'];
      $pereq->customerProfileOrderOverideInd= 'NO';
      $pereq->customerProfileFromOrderInd= 'S';
      $pereq->profileOrderOverideInd = 'Yes';
      $pereq->customerRefNum = $request['ref_number'];
      $pereq->customerAccountType = 'CC';
      $pereq->ccAccountNum = $request['chase_cc_number'];
      if(strlen($request['ccsave_expiration'])==1)
        $Expdate=$request['ccsave_expiration_yr']."0".$request['ccsave_expiration'];
      else
        $Expdate=$request['ccsave_expiration_yr'].$request['ccsave_expiration'];
      $pereq->ccExp = $Expdate;
      $pereq->customerAddress1 = $request['address1'];
      $pereq->customerAddress2 = $request['address2'];
      $pereq->customerCity = $request['city'];
      $pereq->customerState = $request['state'];
      $pereq->customerZIP = $request['zip'];
      $pereq->customerPhone = str_replace("-", "", $request['telephone']);
      $pereq->customerCountryCode = $request['country'];
      $pre->profileChangeRequest = $pereq;
      try
      {
        $soap = new SoapClient($this->getApiGatewayUrl());
      }
      catch (exception $e){
        Mage::throwException($this->parseErrMessage($e->getMessage()));
      }
      try
      {
        $ret = $soap->ProfileChange($pre);
        
      }
      catch (exception $e){
        Mage::throwException($this->parseErrMessage($e->getMessage()));
      }
      return;
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

    /**
     * Generate unique referance number for profile
     * @return integer
     */
    public function GenerateRefNumber()
    {
      $stamp = strtotime("now");
      $ref_numer = $stamp-$_SERVER['REMOTE_ADDR'];
      $ref_numer = str_replace(".", "", $ref_numer);
      return $ref_numer;
    }
  }

