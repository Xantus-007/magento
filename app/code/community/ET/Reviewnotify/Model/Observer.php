<?php
/**
 * ET Web Solutions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_Reviewnotify
 * @copyright  Copyright (c) 2010 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */


class ET_Reviewnotify_Model_Observer
{
	public function __construct()
	{
	}

	public function send_nofificatin_mail($observer)
	{
		$this->need_send = Mage::getStoreConfig('catalog/review/need_send');
		$this->event_email = Mage::getStoreConfig('catalog/review/email_to');
		$this->email_template = Mage::getStoreConfig('catalog/review/email_template');
		$this->email_identity = Mage::getStoreConfig('catalog/review/email_identity');


		if(($this->need_send)&(trim($this->event_email)))
		{
			$product=Mage::getModel('catalog/product')->load($observer->object->getEntityPkValue());
			$emailTemplate = Mage::getModel('core/email_template');

			$recipients = explode(",",$this->event_email);
			foreach($recipients as $k => $recipient)
				$sendresult=$emailTemplate->setDesignConfig(array('area'  => 'backend'))
					->sendTransactional(
						$this->email_template,
						$this->email_identity,
						trim($recipient),
						trim($recipient),
						array(
							"product"=>$product->getName()." (sku: ".$product->getsku().")",
							"title"=>$observer->object->getTitle(),
							"nickname"=>$observer->object->getNickname(),
							"details"=>$observer->object->getDetail(),
							"id"=>$observer->object->getId(),
							'date'  => Mage::app()->getLocale()->date(date("Y-m-d H:i:s"), Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM), null,true)
						)
					);
		}
	}

}