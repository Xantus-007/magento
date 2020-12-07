<?php
namespace Dbm\Wordpress\Starter;

use Dbm\Wordpress\Starter;
use Symfony\Component\Yaml\Yaml;

abstract class ConfigAbstract
{

    const CONFIG_FILENAME = 'config.yml';         

    public function getThemeName()
    {
        return wp_get_theme()->get('TextDomain');
    }

    public function getConfig()
    {                              
        $themeName = $this->getThemeName();        

        $file = WP_CONTENT_DIR . '/themes/' . $themeName . '/'. self::CONFIG_FILENAME;

        if (file_exists($file)) {
            $config = Yaml::parse(file_get_contents($file));
            return $config;
        } else {
            print("The theme config file (config.yml) doesn't exists.");
        }
    } 

}