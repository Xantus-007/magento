<?php

require_once(Mage::getModuleDir('controllers','Mageplaza_BetterBlog').DS.'Adminhtml'.DS.'Betterblog'.DS.'PostController.php');
class Monbento_Site_Adminhtml_Monbento_PostsController extends Mageplaza_BetterBlog_Adminhtml_Betterblog_PostController
{
 
    /**
     * default action for post controller
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function indexAction()
    {
        $cats = $this->getRequest()->getParam('category');
        Mage::getSingleton('admin/session')->setMonbentoBeforeUrl('*/monbento_posts/index/category/'.$cats);
        
        $this->_title($this->__('Better Blog'))
             ->_title($this->__('Manage Posts'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * save post action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $postId   = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $post     = $this->_initPost();
            $postData = $this->getRequest()->getPost('post', array());
            $post->addData($postData);
            $setId = $this->getRequest()->getParam('set', null);
            if(!$setId && !$isEdit)
            {
                $setId = $post->getDefaultAttributeSetId();
            }
            $post->setAttributeSetId($setId);
            if (isset($data['tags'])) {
                $tags = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['tags']);
                $post->setTagsData($tags);
            }
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $post->setCategoriesData($categories);
                }
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $post->setData($attributeCode, false);
                }
            }
            try {
                $post->save();
                $postId = $post->getId();
                $this->_getSession()->addSuccess(
                    Mage::helper('mageplaza_betterblog')->__('Post was saved')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setPostData($postData);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(
                    Mage::helper('mageplaza_betterblog')->__('Error saving post')
                )
                ->setPostData($postData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirectReferer();
        } elseif($beforeUrl = Mage::getSingleton('admin/session')->getMonbentoBeforeUrl()) {
            Mage::getSingleton('admin/session')->setMonbentoBeforeUrl(false);
            $this->_redirect($beforeUrl, array('store'=>$storeId));
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }
}
