<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rauno
 * Date: 16.05.13
 * Time: 9:54
 * To change this template use File | Settings | File Templates.
 */
class Oye_Checkout_IndexController extends Mage_Core_Controller_Front_Action {
    public function quickCartAction() {
        $this->loadLayout();

//        $block = $this->getLayout()->createBlock(
//            'oye_ajaxcart/cart',
//            'ajax.top.cart',
//            array('template' => 'oye/ajaxcart/cart.phtml')
//        );
        $block = Mage::getBlockSingleton('oyecheckout/cart');
        $block->setTemplate('oye/ajaxcart/cart.phtml');
        $block->addItemRender('simple','checkout/cart_item_renderer','oye/ajaxcart/default.phtml');
        $block->addItemRender('grouped','checkout/cart_item_renderer_grouped','oye/ajaxcart/default.phtml');
        $block->addItemRender('configurable','checkout/cart_item_renderer_configurable','oye/ajaxcart/default.phtml');
        $block->addItemRender('bundle','bundle/checkout_cart_item_renderer','oye/ajaxcart/default.phtml');

        $html   = $block->toHtml();
        $this->getResponse()->setBody($html);
    }

    public function quickCartLabelAction() {
        $this->loadLayout();

//        $block = $this->getLayout()->createBlock(
//            'core/template',
//            'top.cart.link',
//            array('template' => 'oye/ajaxcart/top.cart.phtml')
//        );
        $block = $this->getLayout()->createBlock("core/template")
            ->setName('top.cart.link')
            ->setId('top-cart-link')
            ->setTemplate('oye/ajaxcart/top.cart.phtml');

        $html   = $block->toHtml();
        $this->getResponse()->setBody($html);
    }
}