<?php

class Monbento_V2_Model_Observer
{

    public function prepareForm(Varien_Event_Observer $observer)
    {
        $form = $observer->getEvent()->getForm();

        $fieldset = $form->addFieldset('gestion_fieldset', array('legend' => Mage::helper('cms')->__('Gestion CMS')));

        $collection = Mage::getModel('cms/page')->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('identifier', array(array('nin' => array('no-route', 'enable-cookies'))));

        $opt[] = array('label' => '== Aucune ==', 'value' => 0);
        foreach($collection as $v)
        {
            $opt[] = array('label' => '(' . $v['libelle'] . ') ' . $v['title'], 'value' => $v['page_id']);
        }

        $fieldset->addField('parent', 'select', array(
            'name' => 'parent',
            'label' => Mage::helper('cms')->__('Page parente'),
            'title' => Mage::helper('cms')->__('Page parente'),
            'values' => $opt,
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('libelle', 'text', array(
            'name' => 'libelle',
            'label' => Mage::helper('cms')->__('Libellé'),
            'title' => Mage::helper('cms')->__('Libellé'),
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('position', 'text', array(
            'name' => 'position',
            'label' => Mage::helper('cms')->__('Position'),
            'title' => Mage::helper('cms')->__('position'),
            'disabled' => $isElementDisabled,
        ));
    }

    public function savePage(Varien_Event_Observer $observer)
    {
        $model = $observer->getEvent()->getPage();
        if($model->getPageId())
        {
            $request = $observer->getEvent()->getRequest();
            $data = $request->getPost();

            $sql = 'UPDATE cms_page SET ';

            if(!empty($data['parent'][0]))
                $sql .= 'parent = ' . $data['parent'][0];
            else
                $sql .= 'parent = NULL';

            if(!empty($data['libelle']))
                $sql .= ', libelle = "' . $data['libelle'] . '"';
            else
                $sql .= ', libelle = NULL';

            if(!empty($data['position']))
                $sql .= ', position = ' . $data['position'];
            else
                $sql .= ', position = NULL';

            $sql .= ' WHERE page_id = ' . $model->getPageId() . ';';

            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write->query($sql);
        }
    }

    public function productSaveHandler(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if($product->getId())
        {
            Mage::getModel('catalogrule/rule')->getResource()->applyAllRulesForDateRange(null, null, $product);
            
            $event = Mage::getSingleton('index/indexer')->logEvent(
                $product,
                $product->getResource()->getType(),
                Mage_Index_Model_Event::TYPE_SAVE,
                false
            );

            Mage::getSingleton('index/indexer')
                ->getProcessByCode('catalog_product_price') // Adjust the indexer process code as needed
                ->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)
                ->processEvent($event);
        }
    }
    
    public function addressFormatHandler(Varien_Event_Observer $event)
    {
        $type = $event->getType();
        $address = $event->getAddress();
        
        $address->setTelephone($this->_cleanPhone($address->getTelephone()));
        $address->setTelephone($this->_cleanPhone($address->getTelephone()));
    }
    
    protected function _cleanPhone($string)
    {
        return preg_replace('/[^0-9,.]/', '', $string);
    }
}
