<?php

class Dbm_Blog_Block_Manage_Blog_Edit_Form extends AW_Blog_Block_Manage_Blog_Edit_Form
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = new Varien_Data_Form(
            array(
                 'id'     => 'edit_form',
                 'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                 'method' => 'post',
                 'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return $this;
    }
}