<?php
/**
* Wordpress theme Framework
* by DBM - De Bussac Multimédia
*
* @author     Michaël Espeche
*/

namespace Dbm\Wordpress\Starter;

class Image {

    /**
     * Add image sizes
     * @param [array] $sizes Image sizes
     */
    public function addSizes($sizes)
    {
        foreach ($sizes as $image) {            
            add_image_size($image[0], $image[1], $image[2], $image[3]);
        }
    }
}
