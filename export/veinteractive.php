<?php
	define('SAVE_FEED_LOCATION_FR','veinteractive_base_feed_fr.txt');
	define('SAVE_FEED_LOCATION_DE','veinteractive_base_feed_de.txt');
        define('SAVE_FEED_LOCATION_EN','veinteractive_base_feed_en.txt');
        define('SAVE_FEED_LOCATION_ES','veinteractive_base_feed_es.txt');
        define('SAVE_FEED_LOCATION_IT','veinteractive_base_feed_it.txt');
        define('SAVE_FEED_LOCATION_US','veinteractive_base_feed_us.txt');

	// make sure we don't time out
	set_time_limit(0);

	require_once '../app/Mage.php';
        Mage::app();

	try{
		$handle = fopen(SAVE_FEED_LOCATION_FR, 'w');

		$heading = array('unique_id','category1','category2','brand','product_name','description','longdescription','price','product_link','image');
		$feed_line=implode("|", $heading)."\r\n";
		fwrite($handle, $feed_line);

		//---------------------- GET THE PRODUCTS
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addStoreFilter(1)
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
                        $cats = $product->getCategoryIds();
                        $icat = 1;
                        foreach ($cats as $category_id) {
                            $_cat = Mage::getModel('catalog/category')->load($category_id);
                            if($_cat->getLevel() > 1 and $icat < 3) $product_data['category'.$icat]=$_cat->getName();
                            $icat++;
                        }
                    if(!isset($product_data['category1'])) $product_data['category1'] = '';
                    if(!isset($product_data['category2'])) $product_data['category2'] = '';
                    $product_data['brand']='monbento';
		    $product_data['title']=$product->getName();
		    $product_data['description']=$product->getShortDescription();
                    $product_data['long_description']=$product->getDescription();
                    $product_data['price']= round($product->getPrice(),2);
                    $product_data['link']=$baseurl.$product->getUrlPath();
		    $product_data['image_link']=$basemediaurl.'catalog/product'.$product->getImage();

		    //sanitize data
		    foreach($product_data as $k=>$val){
	     	    $bad=array('"',"\r\n","\n","\r","\t");
			      $good=array(""," "," "," ","");
			      $product_data[$k] = '"'.str_replace($bad,$good,$val).'"';
		    }

		    $feed_line = implode("|", $product_data)."\r\n";
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

		$heading = array('unique_id','category1','category2','brand','product_name','description','longdescription','price','product_link','image');
		$feed_line=implode("|", $heading)."\r\n";
		fwrite($handle, $feed_line);

		//---------------------- GET THE PRODUCTS
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addStoreFilter(2)
			->addAttributeToFilter('status', array('eq' => 1))
			->addAttributeToFilter('visibility', array('eq' => 4));

		$prodIds=$products->getAllIds();
		$baseurl = Mage::app()->getStore(2)->getBaseUrl();
		$basemediaurl = Mage::app()->getStore(2)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

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
                        $cats = $product->getCategoryIds();
                        foreach ($cats as $category_id) {
                            $_cat = Mage::getModel('catalog/category')->load($category_id);
                            if($_cat->getLevel() == 2) $product_data['category1']=$_cat->getName();
                            if($_cat->getLevel() == 1) $product_data['category2']=$_cat->getName();
                        }
                    $product_data['brand']='monbento';
		    $product_data['title']=$product->getName();
		    $product_data['description']=$product->getShortDescription();
                    $product_data['long_description']=$product->getDescription();
                    $product_data['price']= round($product->getPrice(),2);
                    $product_data['link']=$baseurl.$product->getUrlPath();
		    $product_data['image_link']=$basemediaurl.'catalog/product'.$product->getImage();

		    //sanitize data
		    foreach($product_data as $k=>$val){
	     	    $bad=array('"',"\r\n","\n","\r","\t");
			      $good=array(""," "," "," ","");
			      $product_data[$k] = '"'.str_replace($bad,$good,$val).'"';
		    }

		    $feed_line = implode("|", $product_data)."\r\n";
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
		$handle = fopen(SAVE_FEED_LOCATION_DE, 'w');

		$heading = array('unique_id','category1','category2','brand','product_name','description','longdescription','price','product_link','image');
		$feed_line=implode("|", $heading)."\r\n";
		fwrite($handle, $feed_line);

		//---------------------- GET THE PRODUCTS
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addStoreFilter(5)
			->addAttributeToFilter('status', array('eq' => 1))
			->addAttributeToFilter('visibility', array('eq' => 4));

		$prodIds=$products->getAllIds();
		$baseurl = Mage::app()->getStore(5)->getBaseUrl();
		$basemediaurl = Mage::app()->getStore(5)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

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
                        $cats = $product->getCategoryIds();
                        foreach ($cats as $category_id) {
                            $_cat = Mage::getModel('catalog/category')->load($category_id);
                            if($_cat->getLevel() == 2) $product_data['category1']=$_cat->getName();
                            if($_cat->getLevel() == 1) $product_data['category2']=$_cat->getName();
                        }
                    $product_data['brand']='monbento';
		    $product_data['title']=$product->getName();
		    $product_data['description']=$product->getShortDescription();
                    $product_data['long_description']=$product->getDescription();
                    $product_data['price']= round($product->getPrice(),2);
                    $product_data['link']=$baseurl.$product->getUrlPath();
		    $product_data['image_link']=$basemediaurl.'catalog/product'.$product->getImage();

		    //sanitize data
		    foreach($product_data as $k=>$val){
	     	    $bad=array('"',"\r\n","\n","\r","\t");
			      $good=array(""," "," "," ","");
			      $product_data[$k] = '"'.str_replace($bad,$good,$val).'"';
		    }

		    $feed_line = implode("|", $product_data)."\r\n";
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
		$handle = fopen(SAVE_FEED_LOCATION_ES, 'w');

		$heading = array('unique_id','category1','category2','brand','product_name','description','longdescription','price','product_link','image');
		$feed_line=implode("|", $heading)."\r\n";
		fwrite($handle, $feed_line);

		//---------------------- GET THE PRODUCTS
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addStoreFilter(4)
			->addAttributeToFilter('status', array('eq' => 1))
			->addAttributeToFilter('visibility', array('eq' => 4));

		$prodIds=$products->getAllIds();
		$baseurl = Mage::app()->getStore(4)->getBaseUrl();
		$basemediaurl = Mage::app()->getStore(4)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

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
                        $cats = $product->getCategoryIds();
                        foreach ($cats as $category_id) {
                            $_cat = Mage::getModel('catalog/category')->load($category_id);
                            if($_cat->getLevel() == 2) $product_data['category1']=$_cat->getName();
                            if($_cat->getLevel() == 1) $product_data['category2']=$_cat->getName();
                        }
                    $product_data['brand']='monbento';
		    $product_data['title']=$product->getName();
		    $product_data['description']=$product->getShortDescription();
                    $product_data['long_description']=$product->getDescription();
                    $product_data['price']= round($product->getPrice(),2);
                    $product_data['link']=$baseurl.$product->getUrlPath();
		    $product_data['image_link']=$basemediaurl.'catalog/product'.$product->getImage();

		    //sanitize data
		    foreach($product_data as $k=>$val){
	     	    $bad=array('"',"\r\n","\n","\r","\t");
			      $good=array(""," "," "," ","");
			      $product_data[$k] = '"'.str_replace($bad,$good,$val).'"';
		    }

		    $feed_line = implode("|", $product_data)."\r\n";
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
		$handle = fopen(SAVE_FEED_LOCATION_IT, 'w');

		$heading = array('unique_id','category1','category2','brand','product_name','description','longdescription','price','product_link','image');
		$feed_line=implode("|", $heading)."\r\n";
		fwrite($handle, $feed_line);

		//---------------------- GET THE PRODUCTS
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addStoreFilter(3)
			->addAttributeToFilter('status', array('eq' => 1))
			->addAttributeToFilter('visibility', array('eq' => 4));

		$prodIds=$products->getAllIds();
		$baseurl = Mage::app()->getStore(3)->getBaseUrl();
		$basemediaurl = Mage::app()->getStore(3)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

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
                        $cats = $product->getCategoryIds();
                        foreach ($cats as $category_id) {
                            $_cat = Mage::getModel('catalog/category')->load($category_id);
                            if($_cat->getLevel() == 2) $product_data['category1']=$_cat->getName();
                            if($_cat->getLevel() == 1) $product_data['category2']=$_cat->getName();
                        }
                    $product_data['brand']='monbento';
		    $product_data['title']=$product->getName();
		    $product_data['description']=$product->getShortDescription();
                    $product_data['long_description']=$product->getDescription();
                    $product_data['price']= round($product->getPrice(),2);
                    $product_data['link']=$baseurl.$product->getUrlPath();
		    $product_data['image_link']=$basemediaurl.'catalog/product'.$product->getImage();

		    //sanitize data
		    foreach($product_data as $k=>$val){
	     	    $bad=array('"',"\r\n","\n","\r","\t");
			      $good=array(""," "," "," ","");
			      $product_data[$k] = '"'.str_replace($bad,$good,$val).'"';
		    }

		    $feed_line = implode("|", $product_data)."\r\n";
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
		$handle = fopen(SAVE_FEED_LOCATION_US, 'w');

		$heading = array('unique_id','category1','category2','brand','product_name','description','longdescription','price','product_link','image');
		$feed_line=implode("|", $heading)."\r\n";
		fwrite($handle, $feed_line);

		//---------------------- GET THE PRODUCTS
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addStoreFilter(6)
			->addAttributeToFilter('status', array('eq' => 1))
			->addAttributeToFilter('visibility', array('eq' => 4));

		$prodIds=$products->getAllIds();
		$baseurl = Mage::app()->getStore(6)->getBaseUrl();
		$basemediaurl = Mage::app()->getStore(6)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

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
                        $cats = $product->getCategoryIds();
                        foreach ($cats as $category_id) {
                            $_cat = Mage::getModel('catalog/category')->load($category_id);
                            if($_cat->getLevel() == 2) $product_data['category1']=$_cat->getName();
                            if($_cat->getLevel() == 1) $product_data['category2']=$_cat->getName();
                        }
                    $product_data['brand']='monbento';
		    $product_data['title']=$product->getName();
		    $product_data['description']=$product->getShortDescription();
                    $product_data['long_description']=$product->getDescription();
                    $product_data['price']= round($product->getPrice(),2);
                    $product_data['link']=$baseurl.$product->getUrlPath();
		    $product_data['image_link']=$basemediaurl.'catalog/product'.$product->getImage();

		    //sanitize data
		    foreach($product_data as $k=>$val){
	     	    $bad=array('"',"\r\n","\n","\r","\t");
			      $good=array(""," "," "," ","");
			      $product_data[$k] = '"'.str_replace($bad,$good,$val).'"';
		    }

		    $feed_line = implode("|", $product_data)."\r\n";
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
