<?php

require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Catalog'.DS.'Product'.DS.'SetController.php');
class Dbm_ExtendBetterBlog_Adminhtml_ExtendBetterBlog_Post_TypeController extends Mage_Adminhtml_Catalog_Product_SetController
{
    protected $_entityTypeId;

    /**
     * predispatch
     *
     * @accees public
     * @return void
     * @author Sam
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_entityTypeId = Mage::getModel('eav/entity')
            ->setType(Mageplaza_BetterBlog_Model_Post::ENTITY)
            ->getTypeId();
    }

    /**
     * default action
     *
     * @accees public
     * @return void
     * @author Sam
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('mageplaza_betterblog')->__('Post'))
             ->_title(Mage::helper('mageplaza_betterblog')->__('Types'))
             ->_title(Mage::helper('mageplaza_betterblog')->__('Manage Post types'));
        
        $this->_setTypeId();

        $this->loadLayout()
            ->_setActiveMenu('mageplaza_betterblog/post_types')
            ->_addBreadcrumb(
                Mage::helper('mageplaza_betterblog')->__('Post'),
                Mage::helper('mageplaza_betterblog')->__('Post')
            )
            ->_addBreadcrumb(
                Mage::helper('mageplaza_betterblog')->__('Manage Post Types'),
                Mage::helper('mageplaza_betterblog')->__('Manage Post Types')
            );
        
        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_toolbar_main'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_grid'));
        
        $this->renderLayout();
    }
    
    public function editAction()
    {
        $this->_title($this->__('Post'))
             ->_title($this->__('Types'))
             ->_title($this->__('Manage Post types'));

        $this->_setTypeId();
        $attributeSet = Mage::getModel('eav/entity_attribute_set')
            ->load($this->getRequest()->getParam('id'));

        if (!$attributeSet->getId()) {
            $this->_redirect('*/*/index');
            return;
        }

        $this->_title($attributeSet->getId() ? $attributeSet->getAttributeSetName() : $this->__('New Set'));

        Mage::register('current_attribute_set', $attributeSet);

        $this->loadLayout();
        $this->_setActiveMenu('mageplaza_betterblog/post_types');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper('catalog')->__('Post'), Mage::helper('catalog')->__('Post'));
        $this->_addBreadcrumb(
            Mage::helper('catalog')->__('Manage Post Types'),
            Mage::helper('catalog')->__('Manage Post Types'));

        $this->_addContent($this->getLayout()->createBlock('dbm_extendbetterblog/adminhtml_post_attribute_set_main'));

        $this->renderLayout();
    }

    /**
     * Define in register catalog_product entity type code as entityType
     *
     */
    protected function _setTypeId()
    {
        Mage::register('entityType',
            Mage::getModel('mageplaza_betterblog/post')->getResource()->getTypeId());
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dbm_extendbetterblog/post_type');
    }
}
