<?php
/**
* Wordpress theme Framework
* by DBM - De Bussac Multimédia
*
* @author     Michaël Espeche
*/

namespace Dbm\Wordpress\Starter;

class Menu {
    protected $_menus;

    /**
     * Constructor
     * @param array $menus Menus list
     */
    public function __construct($menus) {
        $this->_menus = $menus;

        add_action('after_setup_theme', array($this, 'addMenus'));
    }

    public function getMenus()
    {
        return $this->_menus;
    }

    /**
     * Create menus using register_nav_menus function
     */
    public function addMenus()
    {
        foreach ($this->getMenus() as $menu) {
            register_nav_menus($menu);
        }
    }

}
