<?php
/**
* Wordpress theme Framework
* by DBM - De Bussac Multimédia
*
* @author     Michaël Espeche
*/

namespace Dbm\Wordpress\Starter;

use Dbm\Wordpress\Starter\ConfigAbstract;

class Asset extends ConfigAbstract
{
    protected $_cssList;
    protected $_jsList;

    const TYPE_CSS      = 'css';
    const TYPE_JS       = 'js';
    const TYPE_IMAGE    = 'img';
    const TYPE_MEDIA    = 'media';

    /**
     * List of allowed types
     * @return [array] [Allowed types]
     */
    public function getAllowedTypes()
    {
        return array(
            self::TYPE_CSS,
            self::TYPE_JS,
            self::TYPE_IMAGE,
            self::TYPE_MEDIA
        );
    }

    /**
     * Check if asset type is allowed
     * @param  [string]  $type [Type of asset]
     * @return [boolean]       [Type is or is not allowed]
     */
    public function isTypeAllowed($type)
    {
        $types = $this->getAllowedTypes();
        return in_array($type, $types);
    }

    /**
     * Add css file
     * @param [string|array] $css [List of css files]
     */
    public function addCss($css, $deps = array(), $ver = false)
    {
        if(!is_array($css))
        {
            $css = array($css);
        }

        $type = self::TYPE_CSS;

        add_action( 'wp_enqueue_scripts', function() use ($css, $type, $deps, $ver) {
            $this->_add($css, $type, $deps, $ver);
        });
    }

    /**
     * Add js file
     * @param [string|array] $js [List of js files]
     */
    public function addJs($js, $deps = array(), $ver = false)
    {
        if(!is_array($js))
        {
            $js = array($js);
        }

        $type = self::TYPE_JS;

        add_action( 'wp_enqueue_scripts', function() use ($js, $type, $deps, $ver) {
            $this->_add($js, $type, $deps, $ver);
        });
    }

    /**
     * Add image file
     * @param [string|array] $img [List of image files]
     * @return [string] [Image path]
     */
    public function addImg($img)
    {
        if(!is_array($img))
        {
            $img = array($img);
        }

        return $this->_add($img, self::TYPE_IMAGE);
    }

    /**
     * Add media file
     * @param [string|array] $media [List of media files]
     * @return [string] [Media path]
     */
    public function addMedia($media)
    {
        if(!is_array($media))
        {
            $media = array($media);
        }

        return $this->_add($media, self::TYPE_MEDIA);
    }

    /**
    * Add asset file
    * @param [array]   $scripts [Files list]
    * @param [string]  $type    [Type of asset]
     */
    public function _add($scripts, $type, $deps = array(), $ver = false)
    {
        if (is_admin()) {
            return false;
        }

        if (!$this->isTypeAllowed($type)) {
            print("This asset type (" . $type . ") is not allowed.");
            return false;
        }

        // Force $scripts to array
        if(!is_array($scripts))
        {
            $scripts = array($scripts);
        }

        foreach($scripts as $script)
        {
            $name = $this->generateName($script);
            $path = $this->getPath($type);

            switch($type)
            {
                case self::TYPE_JS:
                    wp_register_script($name, $path . $script, $deps, $ver, true);
                    wp_enqueue_script($name);
                break;

                case self::TYPE_CSS:
                    wp_register_style($name, $path . $script, $deps, $ver);
                    wp_enqueue_style($name);
                break;

                case self::TYPE_IMAGE:
                    return $path . $script;
                break;

                case self::TYPE_MEDIA:
                    return $path . $script;
                break;
            }
        }
    }

    /**
     * Auto generate file name
     * @param  [string] $path [File path]
     * @return [string]       [Name (id) of asset]
     */
    protected function generateName($path)
    {
        $data = explode('/', $path);
        end($data);
        $name = $data[key($data)];

        return $name;
    }

    /**
     * Return asset path
     * @param  [string] $type [Asset type]
     * @return [string]       [Asset path]
     */
    public function getPath($type)
    {
        $config = $this->getConfig();

        if (isset($config['assets'])) {
            $dirType = $type . 'Dir';

            if (isset($config['assets'][$dirType])) {
                return get_stylesheet_directory_uri() . '/' . $config['assets'][$dirType];
            } else {
                print("\"" . $dirType . "\" key (of assets key) doesn't exists in your config.yml file.");
                return false;
            }
        } else {
            print("\"assets\" key doesn't exists in your config.yml file.");
            return false;
        }
    }

}
