<?php
/**
* Wordpress theme Framework
* by DBM - De Bussac Multimédia
*
* @author     Michaël Espeche
*/

namespace Dbm\Wordpress\Starter;

class Widget {
    protected $_widgets;

    /**
     * Constructor
     * @param array $widgets Widgets list
     */
    public function __construct($widgets)
    {
        $this->_widgets = $widgets;

        add_action('widgets_init', array($this, 'addWidgets'));
    }

    public function getWidgets()
    {
        return $this->_widgets;
    }

    /**
     * Create widget using register_sidebar function
     */
    public function addWidgets()
    {
        foreach($this->getWidgets() as $options) {
            register_sidebar($options);
        }
    }

}
