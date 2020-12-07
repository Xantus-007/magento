<?php
/**
 * Magento Commnity Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Community Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/community-edition
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
 * @package     Cignex_Paymenttechchase
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/community-edition
 */
class Cignex_Paymenttechchase_Model_NewOrderRequestElement
{
    public $orbitalConnectionUsername;
    public $orbitalConnectionPassword;
    public $transType ;
    public $bin ;
    public $merchantID ;
    public $terminalID ;
    public $amount ;
    public $ccCardVerifyNum ;
    public $ccAccountNum ;
    public $comments ;
    public $orderID ;
    public $industryType ;
    public $avsZip ;
    public $avsName ;
    public $txRefNum ;
    public $addProfileFromOrder;
    public $customerRefNum;
    public $profileOrderOverideInd;
}
