<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_TagAdmin
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */
class IcebergCommerce_TagAdmin_Block_Tags
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Tag
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('tagadmin_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        
        $this->setTemplate('iceberg/tagadmin/tags.phtml');
    }
	
    public function getTabLabel()
    {
    	return 'Tags Quick Add';
    }
    public function getTabTitle()
    {
    	return 'Add Tags to product';	
    }
    public function canShowTab()
    {
    	return !$this->isHidden();
    }
    public function isHidden()
    {
    	return false;
    }
    public function getTagGridBlock()
    {
    	return $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_tag' )->setProductId( $this->getProductId() )->toHtml() ;
    }
    
	public function getProduct()
	{
		if( !$this->getData('product') )
		{
			$this->setData( 'product' , Mage::registry('product') );
		}
			
		return $this->getData('product');
    }
	public function getProductId()
	{
		return $this->getProduct()->getId();
	}
    
    
    public function getAddTagsButton()
    {
    	return $this->getButtonHtml(
            Mage::helper('catalog')->__('Add'),
            //'saveAndContinueEdit(\'' . $this->getSaveAndContinueUrl() . '\');',
            'tagRowControl.addItem()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }
    
	public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }	
}