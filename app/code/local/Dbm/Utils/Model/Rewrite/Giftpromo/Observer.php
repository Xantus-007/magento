<?php

class Dbm_Utils_Model_Rewrite_Giftpromo_Observer extends S3ibusiness_Giftpromo_Model_Observer
{

    public function addSimpleAction($observer)
    {
        if (Mage::getStoreConfig('giftpromo/settings/enabled'))
        {
            $options = array(
                'by_percent' => Mage::helper('salesrule')->__('Percent of product price discount'),
                'by_fixed' => Mage::helper('salesrule')->__('Fixed amount discount'),
                'cart_fixed' => Mage::helper('salesrule')->__('Fixed amount discount for whole cart'),
                'buy_x_get_y' => Mage::helper('salesrule')->__('Buy X get Y free (discount amount is Y)'),
            );
            $giftCollection = Mage::getModel('giftpromo/giftpromo')->getCollection();
            foreach ($giftCollection as $gift)
            {
                if ($gift->getProductId() && $product = $this->getProduct($gift->getProductId()))
                {
                    $options['gift_product_' . $gift->getGiftId()] = Mage::helper('giftpromo')->__("Gift : '%s'", $gift->getGiftName()/* $product->getName() */);
                }
            }

            $form = $observer->getForm();
            $fieldset = $form->getElement('action_fieldset');
            //$data = $fieldset->getElements()[0]->getData();
            $elements = $fieldset->getElements();
            $data = $elements[0]->getData();

            $options = array_merge($data['options'], $options);

            $fieldset->removeField('simple_action');
            $fieldset->addField('simple_action', 'select', array(
                'label' => Mage::helper('salesrule')->__('Apply'),
                'name' => 'simple_action',
                'options' => $options,
                    ), '^');
        }
    }
    
    public function addGiftCart($giftId){
        $gift=Mage::getModel('giftpromo/giftpromo')->load($giftId);
        $this->removeGifts($gift);
        if($gift->getStatus()==1&&($product=$this->getProduct($gift->getProductId()))&&$product->getIsInStock()){
            $cart = Mage::getSingleton('checkout/cart');
            
            try {
                $cart->addProduct($product,1);
                $cart->init();
                $cart->save();
            } catch (Mage_Core_Exception $e) {

            }

        }
    }

    public function addGifts($observer)
    {
        if (Mage::getStoreConfig('giftpromo/settings/enabled'))
        {
            $Controller = $observer->getControllerAction();

            if ($Controller instanceof Mage_Checkout_CartController)
            {
                $cart = Mage::getSingleton('checkout/cart');
                $quote = $cart->getQuote();

                $appliedRuleIds = $quote->getAppliedRuleIds();
                $giftIds = array();
                foreach (explode(',', $appliedRuleIds) as $appliedRuleId)
                {
                    $simpleAction = $rule = Mage::getModel('salesrule/rule')
                                    ->load($appliedRuleId)->getSimpleAction();
                    if (!(stripos($simpleAction, 'gift_product_') === false))
                    {
                        list($g, $p, $giftId) = explode('_', $simpleAction);
                        $this->addGiftCart($giftId);
                        $giftIds[] = $giftId;
                    }
                }
                $this->removeGifts(false, $giftIds);
            }
        }
    }

}
