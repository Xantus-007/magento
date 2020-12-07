<?php

class Dbm_Share_TestController extends Mage_Core_Controller_Front_Action
{
    public function testAction()
    {
        $model = Mage::getModel('dbm_share/element');
    }

    public function scaffoldCatsAction()
    {
        $data = array(
            array(
                'title_fr_fr' => 'titre 01',
                'title_en_gb' => 'title 01',
            ),
            array(
                'title_fr_fr' => 'titre 02',
                'title_en_gb' => 'title 02',
            ),
            array(
                'title_fr_fr' => 'titre 03',
                'title_en_gb' => 'title 03',
            ),
            array(
                'title_fr_fr' => 'titre 04',
                'title_en_gb' => 'title 04',
            ),
        );

        foreach($data as $saveData)
        {
            $model = Mage::getModel('dbm_share/category');

            $model->setData($saveData);
            $model->save();
        }

        echo 'OK';
        exit();
    }

    public function photoAction()
    {
        $element = Mage::getModel('dbm_share/element')->load(2);

        echo $element->getTitleFrFr();

        $photos = Mage::getModel('dbm_share/photo')->getCollection()->addElementFilter($element);

        echo count($photos);

        exit();
    }

    public function deletePhotosAction()
    {
        $element = Mage::getModel('dbm_share/element')->load(2);

        $photos = $element->getPhotos();

        foreach($photos as $photo)
        {
            $photo->delete();
        }

        echo count($photos);
        exit();
    }

    public function getElementAction()
    {
        /*
        $elements = Mage::getModel('dbm_share/element')->getCollection()
            ->addAll()
            ->addTypeFilter('photo')
        ;

        echo $elements->getSelect();

        foreach($elements as $element)
        {
            echo '<pre>'.$element->getTitleFrFr().' : '.$element->getLikeCount().'</pre>';
        }

        exit();
         *
         */
        $type = 'receipe';
        $page = 0;

        $collection = Mage::getModel('dbm_share/element')
            ->getCollection()
            ->setApiDefaults()
            ->addAll()
            ->addTypeFilter($type)
            ->setCurPage($page);
        ;

        //$collection->getSelect()->limit($pageSize, $page * $pageSize);

        foreach($collection as $element)
        {
            $result[] = $element->getData()+array('photos' => $element->getPhotos()->toApiArray());
        }

        print_r($result);
        exit();
    }

    public function likeAction()
    {
        $customer = Mage::getModel('customer/customer')->load(20736);
        $element = Mage::getModel('dbm_share/element')->load(4);

        $element->like($customer);
        //$element->unlike($customer);
    }

    /*
    public function bundleAction()
    {
        $parent = Mage::getModel('catalog/product')->load(3380);
        $optionsCollection = $parent->getTypeInstance(true)->getOptionsCollection($parent);

        $selectionCollection = $parent->getTypeInstance(true)->getSelectionsCollection(
            $parent->getTypeInstance(true)->getOptionsIds($parent),
            $parent
        );

        //$optionsCollection->appendSelections($selectionCollection);

        foreach($optionsCollection as $option)
        {
            //$option = Mage::getModel('catalog/product_option')->load($product->getOptionId());
            print_r($option->getData());
            exit();

            print_r($product->getData());
            exit();

            echo get_class($product);
            exit();
            print_r($product->getData());
            exit();
        }

        echo count($collection);
        exit();
    }
     */

    public function bundle2Action()
    {
        $productId = 3380;
        $result = array();
        $parent = Mage::getModel('catalog/product')->load($productId);

        if($parent->getId() > 0)
        {
            $optionsCollection = $parent->getTypeInstance(true)->getOptionsCollection($parent);

            $selectionCollection = $parent->getTypeInstance(true)->getSelectionsCollection(
                $parent->getTypeInstance(true)->getOptionsIds($parent),
                $parent
            );

            $optionsCollection->appendSelections($selectionCollection);

            foreach($optionsCollection as $option)
            {
                $tmpResult = array(
                    'option_id' => $option->getOptionId(),
                    'parent_id' => $option->getParentId(),
                    'required' => $option->getRequired(),
                    'position' => $option->getPosition(),
                    'type' => $option->getType(),
                    'default_title' => $option->getDefaultTitle(),
                    'selection' => array()
                );

                foreach($option->getSelections() as $child)
                {
                    $tmpResult['selection'][] = array( // Basic product data
                        'product_id' => $child->getId(),
                        'sku'        => $child->getSku(),
                        'name'       => $child->getName(),
                        'set'        => $child->getAttributeSetId(),
                        'type'       => $child->getTypeId(),
                        'category_ids' => $child->getCategoryIds(),
                        'website_ids'  => $child->getWebsiteIds()
                    );
                }

                $result[] = $tmpResult;
            }
        }

        print_r($result);
        exit();
    }

    public function bundle3Action()
    {
        $params = new stdClass();
        $params = array(
            1 => 60,
            2 => 50,
            3 => 42,
            4 => 35,
            5 => 13,
            6 => 1
        );

        $v2 = Mage::getModel('dbm_catalog/api_v2');

        $res = $v2->makeBundleImage(1, 3380, $params);

        print_r($res);
        exit();

        /*
        product	3380
        qty	1
        related_product
        */
        echo 'OK';
        exit();
    }

    public function bundle4Action()
    {
        $v2 = Mage::getModel('dbm_catalog/api_v2');
        
        $res = $v2->getBundledProducts(1, 3380);
        
        print_r($res);
        echo '<pre>END</pre>';
        exit();
    }
    
    public function bundle5Action()
    {
        /*
        //WRONG
        $params = new stdClass();
        $params->options = Mage::helper('dbm_share')->wsdlize(array(
            3 => 45,
            2 => 54,
            1 => 63,
            0 => 0,
            4 => 35,
            5 => 23
        ), true);
        
        $params->qtys = Mage::helper('dbm_share')->wsdlize(array(
            0 => 1,
            1 => 1,
            2 => 1,
            3 => 1,
            4 => 1,
            5 => 1,
        ), true);
        */
        
        /*
        //GOOD
        $params = new stdClass();
        $params->options = Mage::helper('dbm_share')->wsdlize(array(
            3 => 41,
            2 => 52,
            1 => 58,
            6 => 1,
            4 => 29,
            5 => 18,
        ), true);
        
        $params->qtys = Mage::helper('dbm_share')->wsdlize(array(
            1 => 1,
            2 => 1,
            3 => 1,
            4 => 1,
            5 => 1,
            6 => 1
        ), true);
        */
        
        
        //NEW
        $params = new stdClass();
        $params->options = Mage::helper('dbm_share')->wsdlize(array(
            3 => 45,
            2 => 54,
            1 => 63,
            6 => 11,
            4 => 35,
            5 => 23 
        ), true);
        
        $params->qtys = Mage::helper('dbm_share')->wsdlize(array(
            1 => 1,
            2 => 1,
            3 => 1,
            4 => 1,
            5 => 1,
            6 => 1
        ), true);
        
        $v2 = Mage::getModel('dbm_customer/api_v2');
        $res = $v2->addToCart(1, 3380, 1, $params, true);
        
        print_r($res);
        exit();
    }
    
    public function ddlAction()
    {
        $connexion = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connexion->resetDdlCache();
    }
    
    public function categorylistAction()
    {
        $v2 = Mage::getModel('dbm_share/api_v2');
        
        $res = $v2->getCategoryList('photo');
        print_r($res);
    }
    /* DEPRECATED
    public function categoryElementsAction()
    {
        $type = 'receipe';
        $idCategory = 3;
        $page = 0;

        $category = Mage::getModel('dbm_share/category');
        $helper = Mage::helper('dbm_share');
        if($helper->isTypeAllowed($type) && $category->load($idCategory))
        {
            $collection = Mage::getModel('dbm_share/element')->getCollection()
                ->addAll()
                ->setApiDefaults()
                ->addTypeFilter($type)
                ->addCategoryFilter($category)
                ->setCurPage($page)
                ->orderByLikes()
            ;

            echo $collection->getSelect();
            exit();

            $result = $collection->toApiArray();
        }


        print_r($result);
        exit();
    }
     */


    public function customerElementsAction()
    {
        $result = array();
        $idCustomer=  20736;
        $type = 'receipe';
        $helper = Mage::helper('dbm_share');
        $customer = Mage::getModel('customer/customer');

        if($helper->isTypeAllowed($type) && $customer->load($idCustomer))
        {
            $collection =  Mage::getModel('dbm_share/element')->getCollection()
                ->addAll()
                ->setApiDefaults()
                ->addTypeFilter($type)
                ->addCustomerFilter($customer)
                ->setCurPage($page)
                ->orderByDate()
            ;

            $result = $collection->toApiArray();

        }

        print_r($result);
        exit();

        return $result;
    }
    
    public function configAction()
    {
        $config = Mage::helper('dbm_share/api')->getFullConfig('fr_FR');
        
        print_r($config);
        exit();
    }
    
    public function elementAction()
    {
        $v2 = Mage::getModel('dbm_share/api_v2');
        
        $result = $v2->getElementById(23);
        
        print_r($result);
    }
    
    public function categoryelementsAction()
    {
        $v2 = Mage::getModel('dbm_share/api_v2');
        
        $res = $v2->getElementsForCategory(9, 'photo', null, 0);
        
        print_r($res);
        exit();
    }
    
    public function commentAction()
    {
        echo 'hihi';
        exit();
        
        $shareV2 = Mage::getModel('dbm_share/api_v2');
        $session = Mage::getSingleton('customer/session');
        
        //$session->login('vmeron@gmail.com', 'azerty');
        
        $res = $shareV2->comment(2, 'MESSAGE TEST');
        
        if($res)
        {
            echo '<pre>OK</pre>';
        }
        
        echo '<pre>END</pre>';
    }
    
    public function commentCollectionAction()
    {
        $v2 = Mage::getModel('dbm_share/api_v2');
        $comments = $v2->getComments(2);
        
        print_r($comments);
        exit();
    }
    
    public function postAction()
    {
        $this->_login();
        
        $v2 = Mage::getModel('dbm_share/api_v2');
        $photoData = base64_encode(file_get_contents('/Users/vmeron/Desktop/BENTOIMG/vladstudio_raring_ringtail_blue_800x600_signed.jpg'));
        $locale = 'fr_FR';
        $type = 'photo';
        
        $data = array(
            'title' => 'Titre testhihi',
            'photos' => array(
                array(
                    'filename' => 'test.jpg', 
                    'data' => $photoData
                )
            ),
            'categories' => array(2, 3, 7)
        );
        
        $v2->post($type, $locale, $data);
    }
    
    public function followAction()
    {
        $connexion = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connexion->resetDdlCache();
        
        echo '<pre>START</pre>';
        $session = Mage::getSingleton('customer/session');
        $session->login('vmeron@gmail.com', 'azerty');
        
        $v2 = Mage::getModel('dbm_customer/api_v2');
        $res = $v2->unfollow(20730);
        
        var_dump($res);
        
        echo '<pre>END</pre>';
        exit();
    }
    
    public function followersAction()
    {
        $session = Mage::getSingleton('customer/session');
        $session->login('vmeron@gmail.com', 'azerty');
        
        $res = Mage::getModel('dbm_customer/api_v2')->getFollowers(20736);
        
        print_r($res);
        exit();
    }
    
    public function catalogAction()
    {
        $v2 = Mage::getModel('dbm_catalog/catalog_product_api_v2');
        
        $attributes = new stdClass();
        
        $attributes->attributes = array('name', 'price', 'special_price');
        
        $res = $v2->getProductInfo(3394, 1, $attributes, 'id');
        
        print_r($res);
        exit();
    }
    
    public function bundleAction()
    {
        $v2 = Mage::getModel('dbm_catalog/api_v2');
        
        $res = $v2->getBundledProducts(1, 3380);
        
        print_r($res);
        exit();
    }
    
    public function categoryAction()
    {
        $v2 = Mage::getModel('dbm_catalog/catalog_category_api_v2');
        
        $res = $v2->tree(1, 1);
        
        print_r($res);
        exit();
    }
    
    public function treeAction()
    {
        $v2 = Mage::getModel('dbm_catalog/catalog_category_api_v2');
        $res = $v2->tree(45, 1);
        
        print_r($res);
        exit();
    }

    public function localizedAction()
    {
        $element = Mage::getModel('dbm_share/element')->load(2);

        echo $element->getDescription();
    }

    public function prettydateAction()
    {
        $element = Mage::getModel('dbm_share/element')->load(3);

        echo Mage::helper('dbm_share')->getPrettyDate($element->getCreatedAt());
    }
    
    public function receipeAction()
    {
        $connexion = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connexion->resetDdlCache();
        
        $element = Mage::getModel('dbm_share/element')->load(2);
        $element->setLevel('2');
        $element->save();
        
        $element = Mage::getModel('dbm_share/element')->load(2);
        
        print_r($element->getData());
        exit();
    }

    public function popularAction()
    {
        $v2 = Mage::getModel('dbm_share/api_v2');


        $res = $v2->getPopularElements('all', 0);

        print_r($res);
        exit();
    }

    public function listAction()
    {
        $v2 = Mage::getModel('dbm_catalog/catalog_category_api_v2');
        $result = $v2->assignedProducts(48, 1);

        var_dump($result);
        exit();
    }
    
    public function geolocAction()
    {
        $res = Mage::helper('dbm_share')->getGMapsString(49.171155,14.914026);
        echo $res;
    }

    public function addcartAction()
    {
        $this->_login();
        $v2 = Mage::getModel('dbm_customer/api_v2');
        //$res = $v2->emptyCart(1);
        
        /*
        $params = new stdClass();
        $confData = new stdClass();
       
        $confData->key = 76;
        $confData->value = 18;
        
        $params->configurable_data = array(
            $confData
        );
        */
        
        //$res = $v2->addToCart(1, 3415, 3, true);
        
        //$res = $v2->addToCart(1, 3418, 10);
        
        //Configurable
        //01
        $params = new stdClass();
        $paramAttribute = new stdClass();
        $paramAttribute->key = 531;
        $paramAttribute->value = 22;
        $params->super_attribute = array($paramAttribute);
        
        //$v2->addToCart(1, 2713, 1, $params, true);
        
        //02
        $params = new stdClass();
        $paramAttribute = new stdClass();
        $paramAttribute->key = 531;
        $paramAttribute->value = 24;
        $params->super_attribute = array($paramAttribute);
        
        //$v2->addToCart(1, 2713, 1, $params, true);
        
        $cart = Mage::getSingleton('checkout/cart');
        
        foreach($cart->getItems() as $item)
        {
            echo '<pre>'.$item->getId().' -> '.$item->getProduct()->getName().'</pre>';
        }
        
        $v2->setQty(1, 473411, 10, true);
        $v2->setQty(1, 473413, 10, true);
        
        print_r($res);
        exit();
        
        echo $cart->getSummaryQty();
        exit();
    }
    
    public function addCart2Action()
    {
        $v2 = Mage::getModel('dbm_customer/api_v2');
        
        $res = $v2->getCartItems(1);
        
        print_r($res);
        exit();
    }

    public function addcartestAction()
    {
        $this->_login();
        $cart = Mage::getSingleton('checkout/cart');
        
        $cart = Mage::getSingleton('checkout/cart');
        $result = array();
        foreach($cart->getItems() as $item)
        {
            $productId = $item->getProduct()->getId();
            echo '<pre>'.$productId.'</pre>';
            $result[$productId] = 1;
        }

        print_r(array_sum($result));
        exit();
    }

    public function removecartAction()
    {
        $this->_login();
        $v2 = Mage::getModel('dbm_customer/api_v2');
        $res = $v2->removeFromCart(3424);


        var_dump($res);
    }

    public function removecarttestAction()
    {
        $this->_login();
        $cart = Mage::getSingleton('checkout/cart');

        echo $cart->getSummaryQty();
    }

    public function cartitemsAction()
    {
        $this->_login();
        $v2 = Mage::getModel('dbm_customer/api_v2');
        
        $res = $v2->getCartItems(1);
        print_r($res);
        exit();
    }

    public function loginAction()
    {
        $v2 = Mage::getModel('dbm_customer/api_v2');

        $res = $v2->login('vmeron@gmail.com', 'azerty');


        print_r($res);
        exit();
    }
    
    public function login2Action()
    {
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();
        Mage::dispatchEvent('customer_login', array('customer' => $customer));
    }

    public function searchAction()
    {
        $v2 = Mage::getModel('dbm_share/api_v2');
        $res = $v2->search('test', 'receipe', array('fr_FR', 'pt_PT'));

        print_r($res);

        foreach($res as $element)
        {
            echo '<pre>'.$element['type'].'</pre>';
        }
        exit();
    }

    public function searchcustomerAction()
    {
        $v2 = Mage::getModel('dbm_customer/api_v2');
        $res = $v2->search('e', 3);

        print_r($res);
        exit();
    }
    
    public function storesAction()
    {
        $res = Mage::getModel('dbm_store/api_v2')->getStores();
        
        print_r($res);
        exit();
    }

    public function elementboundAction()
    {
        $SW = new stdClass();
        $SW->lat = '45.32754089512459';
        $SW->lng = '2.070732284375026';
        
        $NE = new stdClass();
        $NE->lat = '46.094672635194414';
        $NE->lng = '4.130668807812526';
        
        $bounds = new stdClass();
        $bounds->south_west = $SW;
        $bounds->north_east = $NE;

        $res = Mage::getModel('dbm_share/api_v2')->searchFromBounds($bounds);
        
        print_r($res);
        exit();
    }
    
    public function predictAction()
    {
        $v2 = Mage::getModel('dbm_map/api_v2');
        
        $res = $v2->predict('clerm');
        
        print_r($res);
        exit();
        
    }

    public function sponsorAction()
    {
        $this->_login();
        $customer = Mage::helper('dbm_customer')->getCurrentCustomer();

        $result = Mage::helper('auguria_sponsorship')->getPoints($customer);
        print_r($result);
        exit();
    }

    public function priceAction()
    {
        $cart = Mage::getModel('checkout/cart');

        echo count($cart->getItems());

        foreach($cart->getItems() as $item)
        {
            $item->setPrice(10);
            $item->setBasePrice(10);
            $item->save();
        }

        $cart->save();
    }

    public function sessionAction()
    {
        $session = $_SESSION;
        echo '<pre>'.print_r($_SESSION, true).'</pre>';
        
        exit();
    }
    
    public function blogcatsAction()
    {
        $v2 = Mage::getModel('dbm_blog/api_v2');
        
        $res = $v2->getCategories(1);
        print_r($res);
    }
    
    public function blogpostsAction()
    {
        $v2 = Mage::getModel('dbm_blog/api_v2');
        
        $res = $v2->getPosts(1, 7);
        
        print_r($res);
        exit();
    }
            
    public function blogpostAction()
    {
        $v2 = Mage::getModel('dbm_blog/api_v2');
        
        $result = $v2->getPost(1, 423);
        print_r($result);
    }
    
    public function likedbyAction()
    {
        $v2 = Mage::getModel('dbm_share/api_v2');
        $this->_login();
        
        $res = $v2->getLikedBy('all');
        
        print_r($res);
        exit();
    }
    
    public function flatpostsAction()
    {
        $v2 = Mage::getModel('dbm_blog/api_v2');
        $result = $v2->getFlatPosts(1);
        
        print_r($result);
        exit();
    }
    
    public function fbAction()
    {
        $cookie = '-D1sQDrxtxHPqOBGAjVpL84SkIMGX8eQz3hcvSnBr3g.eyJhbGdvcml0aG0iOiJITUFDLVNIQTI1NiIsImNvZGUiOiJBUUE1c09YalZKaC15NWhIMnBCdGt0eUU4b1VEVE5ReXMzcEF3cmZ0ZFBFV1pZc05DdXY1NGhWVk14OTUteUlINDRvUVVmekpmUVdpOFlXQVVMamwtcnFWSllfUElsb3ctZjJEaTRPOTBlc3FyOVM5ay1pbXYyeXVJSVZLZ3Fwb2t5SDVERTNYR0FYTHNqQmQ5UUdrd3JfN0NwQ2hZWU04ajdMZm40M0VOeDFwM3Ffd1lubjFSX25xTEhQUE5lM0tuUDVQeHlCbTNObjBMc09iSlFBdk9FUHJvSHJzVWZtSU03VFhwbkdBU3ktMVRXdmV6RmdHeVE4emkzcnRrc1Z0eDVFOFNNVExOMGpxdHIzbnVORE5ENktoc0NyVWRtQVg4RlZqUmoxZl9NSUFyZldJbFJLazFSRklBMjRjYkxVNm1mMCIsImlzc3VlZF9hdCI6MTM3MzU2NDY2MSwidXNlcl9pZCI6IjU2NDA0ODM1NiJ9';
        list($encoded_sig, $payload) = explode('.', $cookie, 2);
        
        $sig = $this->_base64_url_decode($encoded_sig);
        $payload = $this->_base64_url_decode($payload); 
        
        print_r(zend_json::decode($payload));
        exit();
        
    }

    public function notificationsAction()
    {
        $v2 = Mage::getModel('dbm_customer/api_v2');
        $v2->login(1, 'vmeron@gmail.com', 'azerty');

        Mage::app()->getStore()->setId(1);
        $v2 = Mage::getModel('dbm_customer/api_v2');
        $res = $v2->getNotifications();
        print_r($res);
        exit();
    }
    
    public function couponAction()
    {
        $coupon = 'DBM2013';
        $v2 = Mage::getModel('dbm_customer/api_v2');
        
        $this->_login();
        
        $res = $v2->addToCart(1, 3418, 15);
        $v2->applyCoupon('DBM2013', 1);
        $res = $v2->getCartItems(1);
        print_r($res);
        exit();
        
    }
    
    public function customerAction()
    {
        $v2 = Mage::getModel('dbm_customer/api_v2');
        $res = $v2->login(1, 'vmeron@gmail.com', 'azerty');
        
        print_r($res);
        exit();
    }

    public function latestAction()
    {
        $v2 = Mage::getModel('dbm_share/api_v2');
        $res = $v2->getLatestElements('receipe', 0);
        print_r($res);
        exit();
    }
    
    public function translateAction()
    {
        $string = "%s liked your %s";
        
        echo $this->__($string, 'hihi', 'hoho');
    }
    
    public function translate2Action()
    {
        $locale = new Zend_Locale('en_US');
        $countries = $locale->getTranslationList('Territory', $locale->getLanguage(), 2);
        
        $this->_translateCountry('Etats-Unis', 'en_US');
        
        echo $countries['DE'];
        echo 'OK';
        
        exit();
    }
    
    protected function _translateCountry($country, $locale)
    {
        $startLocale = new Zend_Locale('fr_FR');
        $frCountries = $startLocale->getTranslationList('Territory', $startLocale->getLanguage(), 2);
        $frCountries = array_flip($frCountries);
        
        $countryCode = $frCountries[$country];
        
        $endLocale = new Zend_Locale('en_US');
        $endCountries = $endLocale->getTranslationList('Territory', $endLocale->getLanguage(), 2);
        $result = $endCountries[$countryCode];
        
        if(strlen($result) == 0)
        {
            $result = $country;
        }
        
        return $result;
    }
    
    public function mylikesAction()
    {
        $this->_login();
        
        $v2 = Mage::getModel('dbm_share/api_v2');
        $res = $v2->getLikedElements('photo', 0);
        print_r($res);
        exit();
    }
    
    public function testLogAction()
    {
        echo 'TEST';
        Mage::log('TEST');
        exit();
    }
    
    public function localeTestAction()
    {
        $currentLocale = 'en_GB';
        $search = 'jp_JP';
        $zLocale = new Zend_Locale($currentLocale);
        $sLocaleData = explode('_', $search);
        $languages = $zLocale->getTranslationList('Language', $localeData[1], 2);
        
        print_r($languages);
        exit();
    }
    
    public function testcronAction(){
        $mod = Mage::getModel('dbm_share/observer');
        $mod->birthdayCronHandler();
    }
    
    public function tempProfileAction()
    {
        $customer = Mage::getModel('customer/customer')->load(38792);
        $data = Mage::helper('dbm_customer')->generateCustomerProfileData($customer);
        
        print_r($data);
        exit();
    }
    
    public function customerTestAction()
    {
        $customer = Mage::getModel('customer/customer')->load(38792);
        
        print_r($customer->getData());
        exit();
    }
    
    public function imageAction()
    {
        $customer = Mage::getModel('customer/customer')->load(39713);
        $cHelper = Mage::helper('dbm_customer');
        $cSizes = Mage::helper('dbm_customer/image')->getSizes();
        $cOptions = Mage::helper('dbm_customer/image')->getOptionsForProfile();
        
        echo '<html><body><img src="'. $cHelper->getCustomerImageUrl($customer, $cSizes['element_list'], $cOptions) .'" alt=""></body></html>';
    }
    
    public function pointerAction()
    {
        $product = Mage::getModel('catalog/product')->load(3482);
        $helper = Mage::helper('dbm_utils/product');
        
        $ids = $helper->getParentProductIds($product);
        
        print_r($ids);
        exit();
        exit();
    }
    
    public function customerpointsAction()
    {
        define('DEBUG', false);
        ini_set('memory_limit', '3G');
        ini_set('max_execution_time', 0);
        $allowedIds = array();
        $startDate = '2013-09-01';
        
        $pointsCustomers = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('id')
            ->addAttributeToFilter('accumulated_points', array('gt' => 0))
        ;
        
        foreach($pointsCustomers as $customer)
        {
            $allowedIds[] = $customer->getId();
        }
        
        $shares = Mage::getModel('dbm_share/element')->getCollection();
        
        foreach($shares as $element)
        {
            $allowedIds[] = $element->getCustomer()->getId();
        }
        
        $allowedIds = array_unique($allowedIds);
        
        $customers = Mage::getModel('customer/customer')->getCollection()
            //->addAttributeToFilter('entity_id', array('in' => $allowedIds))
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', array(
                'from' => $startDate,
                'date' => true
            ))
            ->addAttributeToSelect('*')
            ->addAttributeToSort('entity_id', 'DESC')
        ;
        
        $pageSize = 10;
        $customers->setPageSize($pageSize);
        $curPage = 1;
        $pageCount = ceil($customers->getSize() / $pageSize);
        
        unset($customers);
        
        $this->_resetBirthday();
        
        do
        {
            $customers = $customers = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToSort('entity_id', 'DESC')
                //->addAttributeToFilter('entity_id', array('in' => $allowedIds))
                ->addAttributeToFilter('created_at', array(
                    'from' => $startDate,
                    'date' => true
                ))
                ->setPageSize($pageSize)
                ->setCurPage($curPage)
            ;
            
            foreach($customers as $customer)
            {
                //ORDERS : 
                $orderSum = floor($this->_getOrdersSum($customer,$startDate));
                echo '<pre>CUSTOMER : '.$customer->getId().' -> '.$orderSum.'</pre>';
                
                $tmpCustomer = Mage::getModel('customer/customer')->load($customer->getId());
                echo '<pre>==================== START CUSTOMER '.$tmpCustomer->getId().' '.$tmpCustomer->getFirstname().' '.$tmpCustomer->getLastname().'</pre>';
                
                $data = array();
                $offsetPoint = 10;
                //$sponsor = Mage::helper('auguria_sponsorship')->getPoints($tmpCustomer);
                //$points['points_other'] = $sponsor['accumulated'];
                $data['profile_status'] = 0;
                $data['points_other'] = $orderSum;
                $data['points_receipe'] = $this->_getSharePoints($customer, 'receipe');
                $data['points_photo'] = $this->_getSharePoints($customer, 'photo');
                $data['accumulated_points'] = $data['points_other'] + $data['points_receipe'] + $data['points_photo'];
                
                if(!Mage::helper('dbm_customer')->isValidProfile($tmpCustomer))
                {
                    echo 'CREATING PROFILE DATA FOR ACCOUNT : '.$customer->getId();
                    $profileData = Mage::helper('dbm_customer')->generateCustomerProfileData($tmpCustomer);
                    if($profileData)
                    {
                        echo '<pre>SAVING PROFILE DATA : '.print_r($profileData, true).'</pre>';
                        foreach($profileData as $key => $val)
                        {
                            echo '<pre>SAVING PROFILE DATA KEY : '.$key.' -> '.$val.'</pre>';
                            //$tmpCustomer->setData($key, $val)->getResource()->saveAttribute($customer, $key);
                            $tmpCustomer->setData($key, $val);
                        }
                    }
                }
                else
                {
                    echo '<pre>IS VALID PROFILE : '.strlen($customer->getProfileNickname()).' '.strlen($customer->getProfileImage()).'</pre>';
                }
                
                foreach($data as $attribute => $value)
                {
                    $tmpCustomer->setData($attribute, $value)->getResource()->saveAttribute($tmpCustomer, $attribute);
                }
                
                $tmpCustomer->save();
                
                Mage::helper('dbm_customer')->updateCustomerStatus($tmpCustomer);
                echo '<pre>POINTS['.$tmpCustomer->getId().' - '.$tmpCustomer->getLastname().'] : '.print_r($data, true).'</pre>';
                
                echo'<pre>==================== END CUSTOMER '.$tmpCustomer->getId().' '.$tmpCustomer->getFirstname().' '.$customer->getLastname().'</pre>';
                unset ($data);
            }
            
            unset($customers);
            unset($tmpCustomer);
            
            $curPage++;
            
            if($curPage > 10 && DEBUG)
            {
                break;
            }
        } while($curPage < $pageCount);
        
        echo '<pre>END</pre>';
        exit();
    }
    
    protected function _resetBirthday()
    {
        $endDay = 5;
        $startMonth = 9;
        $endMonth = 12;
        
        $customers = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToFilter('dob', array('to' => date('Y-m-d')))
            ->addExpressionAttributeToSelect('month', 'DATE_FORMAT({{dob}}, "%m")', 'dob')
            ->addExpressionAttributeToSelect('day', 'DATE_FORMAT({{dob}}, "%d")', 'dob')
            ->addfieldToFilter('month', array('gteq' => $startMonth))
            ->addfieldToFilter('month', array('lteq' => $endMonth))
        ;
        
        foreach($customers as $customer)
        {
            $dob = $customer->getDob();
            /*$date = clone(Mage::app()->getLocale()->date());
            $date->set($dob);
            
            echo $date;*/
            $month = $customer->getMonth();
            $day = $customer->getDay();
            
            if(intval($month) == $endMonth && intval($day) > $endDay)
            {
                echo '<pre>BREAKING : '.$dob.'</pre>';
                continue;
            }
            
            //Adding points
            echo '<pre>UPDATING CUSTOMER : '.$customer->getId().'</pre>';
            $customer->setData('accumulated_points', 0);
            $customer->setData('points_other', 0);
            $customer->setData('points_photo', 0);
            $customer->setData('points_receipe', 0);
            $customer->setData('profile_status', 0);
            
            $customer->save();
        }
    }
    
    protected function _getOrdersSum(Mage_Customer_Model_Customer $customer, $startDate)
    {
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addFieldToFilter('total_invoiced', array('gt' => 0))
            ->addFieldToFilter('created_at', array(
                'from' => $startDate,
                'date' => true
            ))
            ->addExpressionFieldToSelect('sum_total', 'SUM({{total_invoiced}})', 'total_invoiced')
        ;
        
        $select = $orders->getSelect();
        
        return $orders->getFirstItem()->getData('sum_total');
        
        /*
        echo $select;
        exit();
        //38264
        
        echo '<pre>====== CUSTOMER : '.$customer->getId().' - '.$orders->getFirstItem()->getSumTotal().' - count: '.count($orders).'</pre>';
        $i = 0;
        foreach($orders as $order)
        {
            echo '<pre>Status : '.$order->getStatus().' => '.$order->getTotalInvoiced().'</pre>';
            if($i >= 1)
            {
                echo 'ERR';
                exit();
            }
            $i++;
        }
        */
    }
    
    protected function _getSharePoints(Mage_Customer_Model_Customer $customer, $type)
    {
        $points = array(
            'receipe' => 10,
            'photo' => 0.1
        );
        
        $size = Mage::getModel('dbm_share/element')->getCollection()
            ->addTypeFilter($type)
            ->addCustomerFilter($customer)
            ->getSize()
        ;
        
        echo '<pre>SIZE '.$type.' : '.$size.'</pre>';
        return $points[$type] * $size;
    }
    
    public function customerpoints2Action()
    {
        $customer = Mage::getModel('customer/customer')->load(38792);
        
        $points = Mage::helper('dbm_customer')->getCustomerPoints($customer);
        
        echo $points;
        exit();
    }
    
    public function onepageAction()
    {
        $model = Mage::getModel('checkout/type_onepage');
        $model->test();
        echo get_class($model);
        exit();
    }
    
    public function birthdayAction()
    {
        $endDay = 5;
        $startMonth = 9;
        $endMonth = 12;
        
        $customers = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToFilter('dob', array('to' => date('Y-m-d')))
            ->addExpressionAttributeToSelect('month', 'DATE_FORMAT({{dob}}, "%m")', 'dob')
            ->addExpressionAttributeToSelect('day', 'DATE_FORMAT({{dob}}, "%d")', 'dob')
            ->addfieldToFilter('month', array('gteq' => $startMonth))
            ->addfieldToFilter('month', array('lteq' => $endMonth))
        ;
        
        foreach($customers as $customer)
        {
            $dob = $customer->getDob();
            /*$date = clone(Mage::app()->getLocale()->date());
            $date->set($dob);
            
            echo $date;*/
            $month = $customer->getMonth();
            $day = $customer->getDay();
            
            if(intval($month) == $endMonth && intval($day) > $endDay)
            {
                echo '<pre>BREAKING : '.$dob.'</pre>';
                continue;
            }
            
            //Adding points
            echo '<pre>UPDATING CUSTOMER : '.$customer->getId().'</pre>';
            $customer = Mage::helper('auguria_sponsorship')->addFidelityPoints($customer, Dbm_Share_Model_Observer::POINT_BIRTHDAY);
            Mage::helper('dbm_customer')->addCustomerPoints($customer, Dbm_Customer_Helper_Data::ATTRIBUTE_POINTS_OTHER, Dbm_Share_Model_Observer::POINT_BIRTHDAY);
            $customer->save();
            
            Mage::helper('dbm_customer')->updateCustomerStatus($customer);
            $customer->save();
        }
        
        echo 'END';
        exit();
    }
    
    public function resizeAction()
    {
        $element = Mage::getModel('dbm_share/element')->load(312);
        $options = Mage::helper('dbm_share/image')->getOptionsForList();
        $sizes = Mage::helper('dbm_share/image')->getSizes();
        $imageUrl = Mage::helper('dbm_share/image')->getElementImageUrl($element, $sizes['grid'], $options);
        
        echo '<img src="'.$imageUrl.'" />';
        exit();
        
        print_r($photo->getData());
        exit();
    }

    public function quoteAction()
    {
        $cart = Mage::getModel('checkout/cart');
        print_r($cart->getQuote()->debug());
        exit();
        
        $cart->setOrigin(1);
        $cart->getQuote()->setData('origin', 1);
        $cart->save();
        exit();
    }
    
    public function orderAction()
    {
        $order = Mage::getModel('sales/order')->load(37915);
        
        print_r($order->getData());
        exit();
    }
    
    /*
    protected function _createQuote($customerId, array $shoppingCart, array  $shippingAddress, array $billingAddress, 
        $shippingMethod, $couponCode = null)
    {
        $customerObj = Mage::getModel('customer/customer')->load($customerId);
        $storeId = $customerObj->getStoreId();
        $quoteObj = Mage::getModel('sales/quote')->assignCustomer($customerObj);
        $storeObj = $quoteObj->getStore()->load($storeId);
        $quoteObj->setStore($storeObj);

        // add products to quote
        foreach($shoppingCart as $part) {
            $productModel = Mage::getModel('catalog/product');
            $productObj = $productModel->setStore($storeId)->setStoreId($storeId)->load($part['PartId']);

            $productObj->setSkipCheckRequiredOption(true);

            try{
                $quoteItem = $quoteObj->addProduct($productObj);
                $quoteItem->setPrice(20);
                $quoteItem->setQty(3);
                $quoteItem->setQuote($quoteObj);                                    
                $quoteObj->addItem($quoteItem);

            } catch (exception $e) {
            return false;
            }

            $productObj->unsSkipCheckRequiredOption();
            $quoteItem->checkData();
        }

        // addresses
        $quoteShippingAddress = new Mage_Sales_Model_Quote_Address();
        $quoteShippingAddress->setData($shippingAddress);
        $quoteBillingAddress = new Mage_Sales_Model_Quote_Address();
        $quoteBillingAddress->setData($billingAddress);
        $quoteObj->setShippingAddress($quoteShippingAddress);
        $quoteObj->setBillingAddress($quoteBillingAddress);

        // coupon code
        if(!empty($couponCode)) $quoteObj->setCouponCode($couponCode); 


        // shipping method an collect
        $quoteObj->getShippingAddress()->setShippingMethod($shippingMethod);
        $quoteObj->getShippingAddress()->setCollectShippingRates(true);
        $quoteObj->getShippingAddress()->collectShippingRates();
        $quoteObj->collectTotals(); // calls $address->collectTotals();
        $quoteObj->setIsActive(0);
        $quoteObj->save();

        return $quoteObj->getId();
    }*/
    
    public function agentAction()
    {
        $agent = strtolower(Mage::helper('core/http')->getHttpUserAgent());
        
        var_dump(strstr($agent, 'iphone'));
        exit();
        
        echo $agent;
        exit();
    }
    
    public function jsonAction()
    {
        $data = array('TEST' => "l'hermitage");
        
        echo json_encode($data, JSON_HEX_APOS);
        exit();
    }
    
    protected function _base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
    
    protected function _login()
    {
        $v2 = Mage::getModel('dbm_customer/api_v2');
        $v2->login(1, 'vmeron@gmail.com', 'azerty');
    }
    
    public function stockAction()
    {
        var_dump(Mage::helper('dbm_country')->isStock('eu'));
        exit();
    }
    
    public function catalogRuleAction()
    {
        $product = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', 3576)->getFirstItem();
        
        
        $observer = new Varien_Object();
        $observer->setEvent(new Varien_Object());
        $observer->getEvent()->setProduct($product);
         
        //Mage::getModel('catalogrule/observer')->applyAllRulesOnProduct($observer)
            //->applyAllRules($observer);
        Mage::getModel('catalogrule/rule')->getResource()->applyAllRulesForDateRange(null, null, $product);
        
        var_dump($product->getFinalPrice());
        exit();
        
        echo '<pre>END : '.Mage::helper('core')->currency($product->getMinimalPrice(),true,false).'</pre>';
        exit();
    }
    
    public function catalogRule02Action()
    {
        $product = Mage::getModel('catalog/product')->load(3576);
        
        $event = Mage::getSingleton('index/indexer')->logEvent(
            $product,
            $product->getResource()->getType(),
            Mage_Index_Model_Event::TYPE_SAVE,
            false
        );
        
        Mage::getSingleton('index/indexer')
            ->getProcessByCode('catalog_product_price') // Adjust the indexer process code as needed
            ->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)
            ->processEvent($event);
        
        echo $product->getFinalPrice();
        
        echo '<pre>END</pre>';
        exit();
    }
}
