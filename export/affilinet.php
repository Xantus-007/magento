<?php

define('SAVE_FEED_LOCATION','/home/www/export/affilinet_feed.txt');

set_time_limit(0);

require_once '/home/www/app/Mage.php';
Mage::app();

try{
  $handle = fopen(SAVE_FEED_LOCATION, 'w');
	
  $heading = array('art_number','Deeplink1','category','title','description','imgUrl','Shipping','UnitPrice');
  $feed_line=implode("\t", $heading)."\r\n";
  fwrite($handle, $feed_line);

  $baseurl = Mage::getBaseUrl();
  $basemediaurl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

  // Products
  $product = Mage::getModel('catalog/product')
  	->getCollection()
 		->addAttributeToFilter('status', 1) //enabled
		->addAttributeToFilter('visibility', 4) //catalog, search
  	->addAttributeToFilter('id', array('nin', array_merge(range(2776, 2789), range(3122, 3127), range(3133, 3139))))
  	->addAttributeToSelect('*');
  $prodIds=$product->getAllIds();

  $product = Mage::getModel('catalog/product');

  foreach($prodIds as $productId) {
    $product->reset();
    $product->load($productId);

    $product_data = array();
    $product_data['sku']=$product->getId();
    $product_data['link']=$baseurl.$product->getUrlPath();
    $cat = $product->getCategoryIds();
    if ($cat[0]) $cat = Mage::getModel('catalog/category')->load($cat[0])->getName();
    else $cat = "";
		$product_data['categorie']=$cat;
		$product_data['titre']=$product->getName();
		$product_data['description']=$product->getGoogleBaseDescription();
		$product_data['image']=$basemediaurl.'catalog/product'.$product->getImage();
		$product_data['livraison']='5.9';
		$product_data['prix']= round($product->getPrice(),2);

    //sanitize data
    foreach($product_data as $k=>$val){
    	$bad=array('"',"\r\n","\n","\r","\t");
    	$good=array(""," "," "," ","");
    	$product_data[$k] = '"'.str_replace($bad,$good,strip_tags($val)).'"';
  	}

  	$feed_line = implode("\t", $product_data)."\r\n";
  	fwrite($handle, $feed_line);
  	fflush($handle);
	}

  fclose($handle);

	exec('zip -r  '.SAVE_FEED_LOCATION.'.zip '.SAVE_FEED_LOCATION);
}
catch(Exception $e){
  die($e->getMessage());
}