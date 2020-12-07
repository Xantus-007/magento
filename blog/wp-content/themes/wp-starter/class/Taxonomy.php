<?php
/**
* Wordpress theme Framework
* by DBM - De Bussac Multimédia
*
* @author     Michaël Espeche
*/

namespace Dbm\Wordpress\Starter;

class Taxonomy {
    public $_taxonomies;

    /**
     * Constructor
     * @param array $postTypes Post types list
     */
    public function __construct($taxonomies)
    {
        $this->_taxonomies = $taxonomies;

        add_action('init', array($this, 'addTaxonomy'));
    }

    public function getTaxonomies()
    {
        return $this->_taxonomies;
    }

    /**
     * Create taxonomy
     */
    public function addTaxonomy()
    {
        foreach ($this->getTaxonomies() as $taxonomy => $options) {

            $labels = array(
                'labels' => array(
                    'name' => $options[1],
                    'singular_name' => $options[2]
                )
            );

            $args = $options[3];

            register_taxonomy($taxonomy, $options[0], array_merge($labels, $args));
        }
    }
}
