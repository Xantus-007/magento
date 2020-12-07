<?php
/**
* Wordpress theme Framework
* by DBM - De Bussac Multimédia
*
* @author     Michaël Espeche
*/

namespace Dbm\Wordpress\Starter;

class PostType {
    public $_postTypes;

    /**
     * Constructor
     * @param array $postTypes Post types list
     */
    public function __construct($postTypes)
    {
        $this->_postTypes = $postTypes;

		add_action('init', array($this, 'addPostType'));
    }

    public function getPostTypes()
    {
        return $this->_postTypes;
    }

    /**
     * Create post types
     */
    public function addPostType()
    {
        foreach ($this->getPostTypes() as $postType => $options) {

            $labels = array(
                'labels' => array(
                    'name' => $options[0],
                    'singular_name' => $options[1]
                )
            );

            $args = $options[2];

            register_post_type($postType, array_merge($labels, $args));
        }
    }
}
