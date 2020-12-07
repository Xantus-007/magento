<?php

class Dbm_ExtendBetterBlog_Block_Adminhtml_Post_Edit_Form extends Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Form
{

    /**
     * prepare form
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Form
     * @author Sam
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        
        $form = new Varien_Data_Form(
            array(
                'id'         => 'edit_form',
                'action'     => $this->getUrl(
                    '*/*/save',
                    array(
                        'id' => $this->getRequest()->getParam('id'),
                        'store' => $this->getRequest()->getParam('store'),
                        'set' => $this->getRequest()->getParam('set')
                    )
                ),
                'method'     => 'post',
                'enctype'    => 'multipart/form-data'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return $this;
    }
}
