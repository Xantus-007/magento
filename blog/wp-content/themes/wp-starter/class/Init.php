<?php
/**
* Wordpress theme Framework
* by DBM - De Bussac Multimédia
*
* @author     Michaël Espeche
*/

namespace Dbm\Wordpress\Starter;

use Symfony\Component\Yaml\Yaml;
use Dbm\Wordpress\Starter\ConfigAbstract;

class Init extends ConfigAbstract
{
    public function init()
    {
        add_action('after_setup_theme', array($this, 'afterSetupHandler'));
        add_action('init', array($this, 'redirectAdminUrl'));
        add_filter('login_errors', array($this, 'changeLoginErrorMessage'));
    }

    public function getThemeName()
    {
        return $this->_themeName;
    }

    /**
     * Setup function to init wordpress
     */
    public function afterSetupHandler()
    {
        $themeName = parent::getThemeName();

        load_theme_textdomain( $themeName, get_stylesheet_directory() . '/languages' );

        add_theme_support('automatic-feed-links' );
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');

        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'gallery',
            'caption',
        ));
    }

    /**
     * Disable core admin url
     */
    public function redirectAdminUrl() {
        $urls = array(
            '/wp/wp-login.php',
            '/wp/wp-login.php/',
            '/wp/wp-login',
            '/wp/wp-login/',
            '/wp/wp-admin',
            '/wp/wp-admin/'
        );

        $requestUrl = $_SERVER['REQUEST_URI'];
        $currentUrl = explode('?', $requestUrl);

        if(!is_user_logged_in() && in_array($currentUrl[0], $urls)) {
            wp_redirect(home_url('/'), 301);
        }
    }

    /*
    * Change login error message
    */
    public function changeLoginErrorMessage($error) {
        $error = __('These credentials are incorrect');
        return $error;
    }
}
