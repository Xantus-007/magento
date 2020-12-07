<?php
        
use Facebook\Facebook;

class Dbm_SocialFeeds_Block_Items extends Mage_Core_Block_Template
{
    public function getInstagramItemsFeed($offset, $limit)
    {
        $helper = Mage::helper('socialfeeds');
        $folder = $this->getInstagramImgFolder();
        $nbToGet = $offset+$limit;
        $photos = array();
        
        $instagramUserId = Mage::getStoreConfig('dbm_feeds_config/instagram_config_general/instagram_user_id');
        $instagramAccessToken = Mage::getStoreConfig('dbm_feeds_config/instagram_config_general/instagram_access_token');
        $redirectUri = Mage::getUrl('dbm-social/index/getcode/');
        
        $url = 'https://api.instagram.com/v1/users/'.$instagramUserId.'/media/recent/?access_token='.$instagramAccessToken.'&count='.$nbToGet;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        if(isset($result->meta->error_message) && ($result->meta->error_message == 'The access_token provided is invalid.' || $result->meta->error_message == 'Missing client_id or access_token URL parameter.'))
        {
            return array();
        }

        $iteratorPreventTooImage = 0;
        if(is_object($result)) {
            foreach ($result->data as $post) {
                if($iteratorPreventTooImage > $nbToGet) break;
                $postId = $post->created_time;
                
                if(!empty($post->images->low_resolution->url)) {
                    $smallSize = Mage::getStoreConfig('dbm_feeds_config/instagram_config_general/instagram_small_size');
                    $mediumSize = Mage::getStoreConfig('dbm_feeds_config/instagram_config_general/instagram_medium_size');
                    
                    $testSize = (!empty($mediumSize)) ? $mediumSize : $smallSize;
                    $imageUploaded = true;
                    
                    if(!$helper->issetCacheImage($postId, $folder, $testSize))
                    {
                        $imageUploaded = $helper->getAndResizeImage($post->images->low_resolution->url, $folder, $postId, $smallSize, $smallSize);
                        
                        if(!empty($mediumSize))
                        {
                            $imageUploaded = $helper->getAndResizeImage($post->images->standard_resolution->url, $folder, $postId, $mediumSize, $mediumSize);
                        }
                    }
                    
                    if($imageUploaded)
                    {
                        $photos[] = $post;
                        $iteratorPreventTooImage++;
                    }
                }
            }
        }

        return array_slice($photos, $offset, $limit);
    }
    
    public function getTwitterItemsFeed($offset, $limit)
    {
        $helper = Mage::helper('socialfeeds');
        $folder = $this->getTwitterImgFolder();
        $nbToGet = max($offset+$limit, 5);
        $tweets = array();

        if(Mage::getStoreConfig('dbm_feeds_config/twitter_config_general/twitter_consumer_key'))
        {
        
            /** Merge arugments with defaults */
            $args = array(
                'screen_name' => Mage::getStoreConfig('dbm_feeds_config/twitter_config_general/twitter_screen_name'),
                'count' => $nbToGet,
                'include_rts' => true,
                'exclude_replies' => true
            );
            
            /** Require the twitter auth class */
            if ( !class_exists('TwitterOAuth') )
                require_once Mage::getBaseDir() . DS . 'lib/Twitter/twitteroauth/twitteroauth.php';

            /** Get Twitter connection */
            $twitterConnection = new TwitterOAuth(
                Mage::getStoreConfig('dbm_feeds_config/twitter_config_general/twitter_consumer_key'),
                Mage::getStoreConfig('dbm_feeds_config/twitter_config_general/twitter_consumer_secret'),
                Mage::getStoreConfig('dbm_feeds_config/twitter_config_general/twitter_access_token'),
                Mage::getStoreConfig('dbm_feeds_config/twitter_config_general/twitter_access_token_secret')
            );

            /** Get tweets */
            $tweets = $twitterConnection->get(
                'statuses/user_timeline',
                $args
            );
            
            if(count($tweets) > 0)
            {
                foreach ($tweets as $tweet)
                {
                    if(isset($tweet->entities->media[0]->media_url)) 
                    {
                        $postId = $tweet->id_str;
                        $imageURL = $tweet->entities->media[0]->media_url;
                        if(!$helper->issetCacheImage($postId, $folder, Mage::getStoreConfig('dbm_feeds_config/twitter_config_general/twitter_small_size')))
                        {
                            $smallSize = Mage::getStoreConfig('dbm_feeds_config/twitter_config_general/twitter_small_size');
                            $mediumSize = Mage::getStoreConfig('dbm_feeds_config/twitter_config_general/twitter_medium_size');
                            
                            $helper->getAndResizeImage($imageURL, $folder, $postId, $smallSize, $smallSize);
                            
                            if(!empty($mediumSize))
                            {
                                $helper->getAndResizeImage($imageURL, $folder, $postId, $mediumSize, $mediumSize);
                            }
                        }
                    }
                }
            }
        }
        
        return array_slice($tweets, $offset, $limit);
    }
    
    public function getFacebookItemsFeed($offset, $limit)
    {
        $helper = Mage::helper('socialfeeds');
        $folder = $this->getFacebookImgFolder();
        $nbToGet = $offset+$limit;
        $posts = array();

        if(Mage::getStoreConfig('dbm_feeds_config/facebook_config_general/facebook_app_id'))
        {
        
            define('FACEBOOK_SDK_V4_SRC_DIR', Mage::getBaseDir() . DS . 'lib/Facebook/');
            // include the facebook sdk
            //if ( !class_exists('Facebook') )
                spl_autoload_unregister(array(Varien_Autoload::instance(), 'autoload'));
                require_once Mage::getBaseDir() . DS . 'lib/Facebook/autoload.php';
                spl_autoload_register(array(Varien_Autoload::instance(), 'autoload'));

            // connect to app
            $config = array();
            $config['app_id'] = Mage::getStoreConfig('dbm_feeds_config/facebook_config_general/facebook_app_id');
            $config['app_secret'] = Mage::getStoreConfig('dbm_feeds_config/facebook_config_general/facebook_app_secret');

            // instantiate
            $facebook = new Facebook($config);
            $access_token = $config['app_id'] . '|' . $config['app_secret'];
            
            // set page id
            $pageid = Mage::getStoreConfig('dbm_feeds_config/facebook_config_general/facebook_page_id');

            // now we can access various parts of the graph, starting with the feed
            $feeds = $facebook->get("/" . $pageid . "/posts", $access_token);
            $feeds = $feeds->getDecodedBody();
            
            $iteratorPreventTooImage = 1;
            
            foreach($feeds['data'] as $post)
            {
                if($iteratorPreventTooImage > $nbToGet) break;
                
                if(array_key_exists('message', $post))
                {
                    $postComplete = $facebook->get("/" . $post['id'] . "/attachments" , $access_token);
                    $postComplete = $postComplete->getDecodedBody();

                    if(is_array($postComplete['data'][0])) $post = array_merge($post, $postComplete['data'][0]);

                    if(isset($post['media']['image']))
                    {
                        if(isset($post['media']['image']['src'])) $imageURL = $post['media']['image']['src'];
                        if(strpos($imageURL, 'url=') !== false)
                        {
                            $imageURL = explode('url=', $imageURL);
                            $imageURL = urldecode($imageURL[1]);
                            $imageURL = str_replace('&cfs=1', '', $imageURL);
                        }
                    }
                    elseif($post['subattachments'])
                    {
                        if(isset($post['subattachments']['data'][0]['media']['image']['src'])) $imageURL = $post['subattachments']['data'][0]['media']['image']['src'];
                    }
                    
                    if(isset($imageURL))
                    {
                        $file_headers = @get_headers($imageURL);
                        if($file_headers[0] == 'HTTP/1.1 200 OK' || $file_headers[0] == 'HTTP/1.0 200 OK')
                        {
                            $post['image'] = true;
                            if(!$helper->issetCacheImage($post['id'], $folder, Mage::getStoreConfig('dbm_feeds_config/facebook_config_general/facebook_small_size')))
                            {
                                $smallSize = Mage::getStoreConfig('dbm_feeds_config/facebook_config_general/facebook_small_size');
                                $mediumSize = Mage::getStoreConfig('dbm_feeds_config/facebook_config_general/facebook_medium_size');
                                
                                $helper->getAndResizeImage($imageURL, $folder, $post['id'], $smallSize, $smallSize);
                                
                                if(!empty($mediumSize))
                                {
                                    $helper->getAndResizeImage($imageURL, $folder, $post['id'], $mediumSize, $mediumSize);
                                }
                            }
                        }
                    }
                    
                    $post['date'] = $this->_getDateSince($post['created_time']);
                
                    $posts[] = $post;
                    $iteratorPreventTooImage++;
                }
            }
        }

        return array_slice($posts, $offset, $limit);
    }
    
    public function getImage($fileName, $folder, $size)
    {
        $folderUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $folder;
        
        return $folderUrl . DS . $size . DS . $fileName . '.jpg';
    }
    
    public function getInstagramImgFolder()
    {
        return 'instagram';
    }
    
    public function getTwitterImgFolder()
    {
        return 'twitter';
    }
    
    public function getFacebookImgFolder()
    {
        return 'facebook';
    }
    
    protected function _getDateSince($date) 
    {
        $timeSince = floor((time() - strtotime($date)) / 3600);
        $timeSince = ($timeSince >= 8760) ? floor($timeSince / 8760).$this->__(' ans') : $timeSince;
        $timeSince = (is_numeric($timeSince) && $timeSince >= 720) ? floor($timeSince / 720).$this->__(' mois') : $timeSince;
        $timeSince = (is_numeric($timeSince) && $timeSince >= 24) ? floor($timeSince / 24).$this->__(' jours') : $timeSince;
        $timeSince = (is_numeric($timeSince) && $timeSince >= 1) ? $timeSince.$this->__('h') : $timeSince;
        $timeSince = ($timeSince == 0) ? $this->__('à l\'instant') : $timeSince;
            
        return $timeSince;
    }
}