<?php
/**
 * Magentweet : Twitter Widget for Magento
 * by
 * Agence Dn'D - Cration site e-Commerce Magento - http://www.dnd.fr/magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * Available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Widget
 * @package    Dnd_Magentweet
 * @copyright  Copyright (c) 2010 Agence Dn'D (http://www.dnd.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Dnd_Magentweet_Model_User extends Mage_Core_Model_Abstract
{
  	protected $_urlImg;
  	protected $_followers;
  	protected $_following;
	protected $_name;
	protected $_tuser;
	protected $_nb;
	protected $_link;
	protected $_link_a;
	protected $_desa;
	protected $_url;
	
	public function loading($tuser,$nb,$link,$link_a,$desa)
	{
		$this->_tuser= $tuser;
		$this->_nb= $nb;
		$this->_link= $link;
		$this->_link_a= $link_a;
		$this->_desa= $desa;
		
		$url = 'http://api.twitter.com/1/users/show/'.$tuser.'.xml';
        $tweet = simplexml_load_file($url);
        
        $this->_urlImg = $tweet->profile_image_url;
		$this->_followers = $tweet->followers_count;
		$this->_name = $tweet->name;
		$this->_following = $tweet->friends_count;
		$this->_url = $tweet->url;
		
		return $this;
	}
	
	public function getStatuses()
	{
	    $urlFollow = 'http://twitter.com/statuses/user_timeline/'.$this->_tuser.'.xml';
        $tweet = simplexml_load_file($urlFollow);
        $i=0;
        foreach($tweet->status as $key)
	    {
		      if($i<$this->_nb)
		      {
		      		if($this->_desa=="enable")
		      		{
		      			$flux[] = Mage::helper('magentweet')->transform($key->text,$this->_link,$this->_link_a);
		      		}
		      		else
		      		{
		      			$flux[] = Mage::helper('magentweet')->desactivelink($key->text);
		      		}
		      }
		           
		      $i++;
	    }
		
		return $flux;
	}
	
   	public function getImgUrl()
   	{
   		return $this->_urlImg;
   	}
   
    public function getFollowers()
   	{
   		return $this->_followers;
   	}
   	
   	public function getFollowing()
   	{
   		return $this->_following;
   	}
   	
   	public function getUrl()
   	{
   		return $this->_url;
   	}
   	
   	public function getName()
   	{
   		return $this->_name;
   	}	

}