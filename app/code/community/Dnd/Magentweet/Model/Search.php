<?php
/**
 * Magentweet : Twitter Widget for Magento
 * by
 * Agence Dn'D - Création site e-Commerce Magento - http://www.dnd.fr/magento
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

class Dnd_Magentweet_Model_Search extends Mage_Core_Model_Abstract
{
	protected $_keyw;
	protected $_nb;
	protected $_link;
	protected $_link_a;
	protected $_desa;
	protected $_slang;

	
	public function loading($keyw,$nb,$link,$link_a,$desa,$slang)
	{
		$this->_keyw= $keyw;
		$this->_nb= $nb;
		$this->_link= $link;
		$this->_link_a= $link_a;
		$this->_desa= $desa;
		$this->_slang= $slang;
		
		return $this;
	}
	
	public function getResults()
	{
	    
	    if($this->_slang=="All")
	    {
	    	$url = "";
	    }
	    else
	    {
	    	$iso = substr($this->_slang,0,2);
	    	$url = "lang=".$iso."&";	
	    }
	    
	    $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, "http://search.twitter.com/search.atom?".$url."q=" . urlencode($this->_keyw) . "&amp;amp;amp;rpp=10");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec ($curl);
        curl_close ($curl);
        
        
 		$i=0;
        $tweet = simplexml_load_string($result);
        Mage::log($tweet);
        if($tweet->entry)
        {
        foreach($tweet->entry as $key)
		{
			if($i<$this->_nb)
		    {
		    	if($this->_desa=="enable")
		      	{
		      		$flux[$i]['content'] = Mage::helper('magentweet')->transform($key->title,$this->_link,$this->_link_a);
		      		$flux[$i]['img'] = $key->link[1]['href'];
		      		$flux[$i]['link'] = $key->link[0]['href'];
		      	}
		      	else
		      	{
		      		$flux[$i]['content'] = Mage::helper('magentweet')->desactivelink($key->title);
		      		$flux[$i]['img'] = $key->link[1]['href'];
		      		$flux[$i]['link'] = $key->link[0]['href'];
		      	}
		     }      
		     $i++;	
		     Mage::log("1");	
	    }
		}
		else
		{
			$flux="No result";
		}
		
		return $flux;
	}

   	
}