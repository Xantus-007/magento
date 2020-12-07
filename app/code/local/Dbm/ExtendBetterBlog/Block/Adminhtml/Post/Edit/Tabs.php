<?php

class Dbm_ExtendBetterBlog_Block_Adminhtml_Post_Edit_Tabs extends Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tabs
{

    /**
     * prepare the layout
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tabs
     * @author Sam
     */
    protected function _prepareLayout()
    {
        $post = $this->getPost();
        
        if (!($setId = $post->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        
        if ($setId) {
            $entity = Mage::getModel('eav/entity_type')
                ->load('mageplaza_betterblog_post', 'entity_type_code');
            $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                    ->setEntityTypeFilter($entity->getEntityTypeId());
            $attributes->addFieldToFilter(
                'attribute_code',
                array(
                    'nin' => array('meta_title', 'meta_description', 'meta_keywords')
                )
            );
            $attributes->getSelect()->order('additional_table.position', 'ASC');

            $this->addTab(
                'info',
                array(
                    'label'   => Mage::helper('mageplaza_betterblog')->__('Post Information'),
                    'content' => $this->getLayout()->createBlock(
                        'mageplaza_betterblog/adminhtml_post_edit_tab_attributes'
                    )
                    ->setAttributes($attributes)
                    ->toHtml(),
                )
            );
            $seoAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entity->getEntityTypeId())
                ->addFieldToFilter(
                    'attribute_code',
                    array(
                        'in' => array('meta_title', 'meta_description', 'meta_keywords')
                    )
                );
            $seoAttributes->getSelect()->order('additional_table.position', 'ASC');

            $this->addTab(
                'meta',
                array(
                    'label'   => Mage::helper('mageplaza_betterblog')->__('Meta'),
                    'title'   => Mage::helper('mageplaza_betterblog')->__('Meta'),
                    'content' => $this->getLayout()->createBlock(
                        'mageplaza_betterblog/adminhtml_post_edit_tab_attributes'
                    )
                    ->setAttributes($seoAttributes)
                    ->toHtml(),
                )
            );
            $this->addTab(
                'categories',
                array(
                    'label' => Mage::helper('mageplaza_betterblog')->__('Categories'),
                    'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
                    'class' => 'ajax'
                )
            );
            $this->addTab(
                'tags',
                array(
                    'label' => Mage::helper('mageplaza_betterblog')->__('Tags'),
                    'url'   => $this->getUrl('*/*/tags', array('_current' => true)),
                    'class' => 'ajax'
                )
            );
        } else {
            $this->addTab('set', array(
                'label'     => Mage::helper('catalog')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('dbm_extendbetterblog/adminhtml_post_edit_tab_settings')->toHtml()),
                'active'    => true
            ));
        }
        //return parent::_prepareLayout();
        return parent::_beforeToHtml();
    }
    
    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
