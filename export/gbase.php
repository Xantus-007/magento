<?php
	define('SAVE_FEED_LOCATION','/home/www/export/google_base_feed.txt');
	define('SAVE_FEED_LOCATION_EN','/home/www/export/google_base_feed_en.txt');

	// make sure we don't time out
	set_time_limit(0);

	require_once '/home/www/app/Mage.php';
        Mage::app();

	try{
		$handle = fopen(SAVE_FEED_LOCATION, 'w');

		$heading = array('id','gtin','title','color','material','pattern','google_product_category','product_type','size','brand','description','link','image_link','additional_image_link','price','condition','availability');
		$feed_line=implode("\t", $heading)."\r\n";
		fwrite($handle, $feed_line);

		//---------------------- GET THE PRODUCTS
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addStoreFilter(1)
			->addAttributeToFilter('gbase_in', array('eq' => 1))
			->addAttributeToFilter('status', array('eq' => 1))
			->addAttributeToFilter('visibility', array('eq' => 4));

		$prodIds=$products->getAllIds();
		$baseurl = Mage::app()->getStore(1)->getBaseUrl();
		$basemediaurl = Mage::app()->getStore(1)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

		//echo 'Product filter: '.memory_get_usage(false).'<br>';
		//flush();

		$product = Mage::getModel('catalog/product');

		foreach($prodIds as $productId) {
		    //echo '. ';
		    //flush();
		    //echo 'Loop start: '.memory_get_usage(false).'<br>';
		    //flush();

		    //$product = Mage::getModel('catalog/product');
		    $product->reset();
		    $product->load($productId);

		    $product_data = array();
		    $product_data['sku']=$product->getSku();
		    $product_data['gtin']=$product->getCodeEan();
		    $product_data['title']=$product->getName();
		    $product_data['color']=$product->getGbaseColor();
		    $product_data['material']=$product->getGbaseMaterial();
		    $product_data['pattern']=$product->getGbasePattern();
		    $product_data['google_product_category']=$product->getGbaseProductCategory();
		    $product_data['product_type']=$product->getGbaseProductType();
		    $product_data['size']=$product->getGbaseSize();
			  $product_data['brand']=$product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($product)=='No'?'':$product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($product);
		    $product_data['description']=$product->getGoogleBaseDescription();
			  $product_data['link']=$baseurl.$product->getUrlPath();
		    $product_data['image_link']=$basemediaurl.'catalog/product'.$product->getImage();
		    $i=1;
		    foreach ($product->getMediaGalleryImages() as $value) {
		    	  $product_data['additional_image_link'][]=$basemediaurl.'catalog/product'.$value->getFile();
		    	  if ($i==10) break;
		    	  else $i++;
		    }
		    $product_data['additional_image_link'] = implode(',', $product_data['additional_image_link']);
		    $product_data['price']= round($product->getPrice(),2);

  			$product_data['condition']="new";
			$stockItem = $product->getStockItem();
		    $product_data['availability']=$stockItem->getIsInStock()?'in stock':'out of stock';

		    //sanitize data
		    foreach($product_data as $k=>$val){
	     	    $bad=array('"',"\r\n","\n","\r","\t");
			      $good=array(""," "," "," ","");
			      $product_data[$k] = '"'.str_replace($bad,$good,$val).'"';
		    }

		    $feed_line = implode("\t", $product_data)."\r\n";
		    fwrite($handle, $feed_line);
		    fflush($handle);

		    //echo 'Loop end: '.memory_get_usage(false).'<br>';
		    //flush();
		}

		//---------------------- WRITE THE FEED
		fclose($handle);

	}
	catch(Exception $e){
		die($e->getMessage());
	}

	try{
		$handle = fopen(SAVE_FEED_LOCATION_EN, 'w');


		$heading = array('id','gtin','title','color','material','pattern','google_product_category','product_type','size','brand','description','link','image_link','additional_image_link','price','condition','availability');
		$feed_line=implode("\t", $heading)."\r\n";
		fwrite($handle, $feed_line);

		//---------------------- GET THE PRODUCTS
		$products = Mage::getModel('catalog/product')
				->getCollection()
				->addStoreFilter(2)
				->addAttributeToFilter('gbase_in', array('eq' => 1))
				->addAttributeToFilter('status', array('eq' => 1))
			->addAttributeToFilter('visibility', array('eq' => 4));
		$prodIds=$products->getAllIds();
		$baseurl = Mage::app()->getStore(2)->getBaseUrl();
		$basemediaurl = Mage::app()->getStore(2)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

		//echo 'Product filter: '.memory_get_usage(false).'<br>';
		//flush();

		$product = Mage::getModel('catalog/product')->setStoreId(2);

		foreach($prodIds as $productId) {
		    //echo '. ';
		    //flush();
		    //echo 'Loop start: '.memory_get_usage(false).'<br>';
		    //flush();

		    //$product = Mage::getModel('catalog/product');
		    //$product->reset();
			$product = Mage::getModel('catalog/product')->setStoreId(2);
		    $product->load($productId);

		    $product_data = array();
		    $product_data['sku']=$product->getSku();
		    $product_data['gtin']=$product->getCodeEan();
		    $product_data['title']=$product->getName();
		    $product_data['color']=$product->getGbaseColor();
		    $product_data['material']=$product->getGbaseMaterial();
		    $product_data['pattern']=$product->getGbasePattern();
		    $product_data['google_product_category']=$product->getGbaseProductCategory();
		    $product_data['product_type']=$product->getGbaseProductType();
		    $product_data['size']=$product->getGbaseSize();
			  $product_data['brand']=$product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($product)=='No'?'':$product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($product);
		    $product_data['description']=$product->getGoogleBaseDescription();
			  $product_data['link']=$baseurl.$product->getUrlPath();
		    $product_data['image_link']=$basemediaurl.'catalog/product'.$product->getImage();
		    $i=1;
		    foreach ($product->getMediaGalleryImages() as $value) {
		    	  $product_data['additional_image_link'][]=$basemediaurl.'catalog/product'.$value->getFile();
		    	  if ($i==10) break;
		    	  else $i++;
		    }
		    $product_data['additional_image_link'] = implode(',', $product_data['additional_image_link']);
		    $product_data['price']= round($product->getPrice(),2);

  		  $product_data['condition']="new";
			$stockItem = $product->getStockItem();
		    $product_data['availability']=$stockItem->getIsInStock()?'in stock':'out of stock';

		    //sanitize data
		    foreach($product_data as $k=>$val){
			      $bad=array('"',"\r\n","\n","\r","\t");
			      $good=array(""," "," "," ","");
			      $product_data[$k] = '"'.str_replace($bad,$good,$val).'"';
		    }

		    $feed_line = implode("\t", $product_data)."\r\n";
		    fwrite($handle, $feed_line);
		    fflush($handle);

		    //echo 'Loop end: '.memory_get_usage(false).'<br>';
		    //flush();
		}

		//---------------------- WRITE THE FEED
		fclose($handle);

	}
	catch(Exception $e){
		die($e->getMessage());
	}