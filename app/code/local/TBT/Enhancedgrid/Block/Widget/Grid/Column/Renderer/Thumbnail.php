<?php

class TBT_Enhancedgrid_Block_Widget_Grid_Column_Renderer_Thumbnail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        
        return $this->_getValue($row);
    }
    
    protected function _getValue(Varien_Object $row)
    {
        $product = Mage::getModel('catalog/product')->load($row->entity_id);
        $thumb_url = Mage::getModel('catalog/product_media_config')
                ->getMediaUrl( $product->getImage() );
        
        return '<img src="' . $thumb_url . '" alt="' . $product->name . '" title="' . $product->name . '" style="width: 80px;">';
    }


}
