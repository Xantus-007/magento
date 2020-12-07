<?php

/**
 * Class Wizkunde_SoapSSO_Block_Adminhtml_Server_Edit_Form
 */
class Wizkunde_ConfigurableBundle_Block_Adminhtml_Images_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('configurablebundle_images_form');
        $this->setTitle($this->__('Composite Image Information'));
    }

    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('configurablebundle');

        $form = new Varien_Data_Form(
            array(
            'enctype'   => 'multipart/form-data',
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'    => 'post'
            )
        );

        $infoFieldset = $form->addFieldset(
            'base_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Composite Image Information'),
            'class'     => 'fieldset-wide',
            )
        );

        if ($model->getId()) {
            $infoFieldset->addField(
                'id', 'hidden', array(
                'name' => 'id',
                )
            );
        }

        $infoFieldset->addField(
            'name', 'text', array(
            'name'      => 'name',
            'class'     => 'required-entry validate-alphanum-with-spaces',
            'label'     => Mage::helper('configurablebundle/bundle')->__('Name'),
            'title'     => Mage::helper('configurablebundle/bundle')->__('Name'),
            'required'  => true,
            )
        );

        $imageFieldset = $form->addFieldset(
            'image_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Composite Images'),
            'class'     => 'fieldset-wide',
            )
        );

        $imageFieldset->addField(
            'images', 'gallery', array(
            'name'      => 'images',
            'id'        => 'images',
            'class'     => 'required-entry validate-alphanum',
            'label'     => Mage::helper('configurablebundle/bundle')->__('Images'),
            'title'     => Mage::helper('configurablebundle/bundle')->__('Images'),
            'required'  => true,
            )
        );

        foreach($model->getExistingImages() as $imageId => $image) {
            $imageFieldset->addField(
                'image' . $imageId, 'image', array(
                'name'      => 'image' . $imageId,
                'id'        => 'image' . $imageId,
                'label'     => Mage::helper('configurablebundle/bundle')->__('Existing Image'),
                'title'     => Mage::helper('configurablebundle/bundle')->__('Existing Image')
                )
            );

            $model->addData(array('image' . $imageId => 'configurablebundle' . DS . $image));
        }



        $productFieldset = $form->addFieldset(
            'productsselector_form',
            array(
                'legend' => Mage::helper('configurablebundle/bundle')->__('Products'),
                'class'     => 'fieldset-wide'
            )
        );

        $productFieldset->addField(
            'product_sku', 'text', array(
            'label' => 'Product(s)',
            'name' => 'product_sku',
            'required' => true,
            'class' => 'rule_conditions_fieldset',
            'readonly' => true,
            'onclick' => $this->getProductChooserURL(),
            )
        );

        $productFieldset->addField(
            'trigger', 'button', array(
            'name' => 'trigger',
            'class' => 'scalable add',
            'style' => 'width:100px;',
            'onclick' => $this->getProductChooserURL(),
            )
        );

        $productFieldset->addFieldset('product_chooser', array('legend' => ('')));
        if (Mage::getSingleton('adminhtml/session')->getProductsselectorData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getProductsselectorData());
            Mage::getSingleton('adminhtml/session')->setProductsselectorData(null);
        } elseif (Mage::registry('productsselector_data')) {
            $form->setValues(Mage::registry('productsselector_data')->getData());
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getProductChooserURL() 
    {
        return 'getProductChooser(\'' . Mage::getUrl(
            'adminhtml/promo_widget/chooser/attribute/sku/form/rule_conditions_fieldset', array('_secure' => Mage::app()->getStore()->isAdminUrlSecure())
        ) . '?isAjax=true\'); return false;';
    }
}