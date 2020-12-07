<?php

class Monbento_Site_Model_Rewrite_Catalin_SEO_Catalog_Layer_Filter_Attribute extends Catalin_SEO_Model_Catalog_Layer_Filter_Attribute
{

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        if (!Mage::helper('catalin_seo')->isEnabled()) {
            return parent::_getItemsData();
        }

        $attribute = $this->getAttributeModel();

        $key = $this->getLayer()->getStateKey() . '_' . $this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $attrUrlKeyModel = Mage::getResourceModel('catalin_seo/attribute_urlkey');
            $options = $attribute->getFrontend()->getSelectOptions();
            $optionsCount = $this->_getResource()->getCount($this);
            $data = array();
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }
                if (Mage::helper('core/string')->strlen($option['value'])) {
                    // Check filter type
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']])) {
                            $data[] = array(
                                'label' => $attrUrlKeyModel->getUrlValue($attribute->getId(), $option['value'], 1),
                                'value' => $attrUrlKeyModel->getUrlValue($attribute->getId(), $option['value']),
                                'count' => $optionsCount[$option['value']],
                            );
                        }
                    } else {
                        $data[] = array(
                            'label' => $attrUrlKeyModel->getUrlValue($attribute->getId(), $option['value'], 1),
                            'value' => $attrUrlKeyModel->getUrlValue($attribute->getId(), $option['value']),
                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                        );
                    }
                }
            }

            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG . ':' . $attribute->getId()
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        
        return $data;
    }

}
