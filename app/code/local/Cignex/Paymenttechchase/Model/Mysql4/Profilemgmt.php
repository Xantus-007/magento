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
 * @category    Cignex
 * @package     Cignex_Paymentchase
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Cignex_Paymenttechchase_Model_Mysql4_Profilemgmt extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_chaseprofile="chase_profile";
    public function _construct()
    {    
        $this->_init('paymenttechchase/profilemgmt', 'accountmgmt_id');
    }

    /**
     * Add profile details into database
     * @ Param $custref => Customer ID in database
     * @ Param $ref_number => Referance number of profile in paymentchase
     * @ Param $profileName => Profile name
     */
    public function addProfileName($custref,$ref_number,$profileName)
    {
      $write = Mage::getSingleton('core/resource')->getConnection('core_write');
      $write = $this->_getWriteAdapter();
      $values = array(
          'customer_id'  =>  $custref,
          'ref_number'  =>  $ref_number,
          'profilename'  =>  $profileName,
        );
      $profile = $write->insert($this->_chaseprofile,$values);
    }

    /**
     * Get Profile name from database
     * @Param $ref_number => Referance number of profile in paymentchase
     * @return string
     */
    public function getProfile($ref_number)
    {
      $read = $this->_getReadAdapter();
      $select = $read->select()
              ->from(array('c'=>$this->_chaseprofile),'profilename')
              ->where('ref_number=?',$ref_number);
          Mage::log('select query'.$select);
      $result = $read->fetchAll($select);
      return $result;
    }

    /**
     * Get Profile details from database
     * @Param $ref_number => Referance number of profile in paymentchase
     */
    public function getProfileDetails($ref_number)
    {
      $read = $this->_getReadAdapter();
      $select = $read->select()
              ->from($this->_chaseprofile)
              ->where('ref_number=?',$ref_number);
          Mage::log('select query'.$select);
      $result = $read->fetchAll($select);
      return $result;
    }

    /**
     * Delete Profile from database
     * @Param $ref_number => Referance number of profile in paymentchase
     */
    public function deleteProfileName($ref_number)
    {
      $condition = $this->_getWriteAdapter()->quoteInto("ref_number=?", $ref_number);
      $this->_getWriteAdapter()->delete($this->_chaseprofile, $condition);
      return;
    }

    /**
     * Get Profile referance number from database
     * @Param $ProfileName => Profile name
     * @Param $custId => Customer ID
     * @return integer
     */
    public function getCustomerRefNumber($custId,$ProfileName)
    {
      $CustomerRefNumber = '';
      $read = $this->_getReadAdapter();
      $select = $read->select()
              ->from(array('c'=>$this->_chaseprofile),'ref_number')
              ->where('customer_id=?',$custId)
              ->where('profilename=?',$ProfileName);
      $CustomerRefNumber = $read->fetchOne($select);
      return $CustomerRefNumber;
    }
}