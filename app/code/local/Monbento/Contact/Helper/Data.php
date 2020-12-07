<?php

class Monbento_Contact_Helper_Data extends Mage_Core_Helper_Abstract 
{
    /**
     * Define json conditions list
     *
     * @var array
     */
    protected $conditions;

    /**
     * Store subjects value
     *
     * @var array
     */
    protected $configValue;

    /**
     * Array of subjects category
     *
     * @var array
     */
    protected $options;

    public function __construct()
    {
        $configValue = Mage::getStoreConfig('contacts/monbentocontact/subjects');
        if ($configValue) {
            $this->configValue = unserialize($configValue);
        }

        $this->conditions = array(
            '1' => json_encode(array(
                'ordernum' => 'required'
            )),
            '2' => json_encode(array(
                'photo' => 'optional',
                'ordernum' => 'requiredOrder',
                'invoice' => 'requiredOrder',
            )),
            '3' => json_encode(array(
                'company' => 'required',
                'city' => 'required',
                'country' => 'required',
            )),
            '4' => json_encode(array(
                'country' => 'required',
                'media' => 'required',
            ))
        );

        $this->options = array(
	        '' => '',
            'order' => $this->__('My order'),
            'pro' => $this->__('Professionals'),
            'product' => $this->__('Products'),
            'other' => $this->__('Other'),
	    );
    }

    /**
     * Get contact page introduction
     *
     * @return  string
     */
    public function getIntro()
    {
        return Mage::getStoreConfig('contacts/monbentocontact/intro_message');
    }

    /**
     * Get subject line by Email
     *
     * @param   string $key
     * @return  array
     */
    public function getSubjectByKey($key)
    {
        foreach ($this->configValue as $_key => $value) {
            if ($key == $_key)
                return $value;
        }
    }

    /**
     * Get subject values 
     *
     * @return  array
     */
    public function getSubjectValues()
    {
        $data = [];
        foreach ($this->configValue as $key => $value) {
            $cat = $value['category'];
            if (isset($this->options[$cat])) {
                $cat = Mage::helper('monbentocontact')->__($this->options[$cat]);
                $condition = isset($this->conditions[$value['condition']]) ? $this->conditions[$value['condition']] : '';
                $data[$cat][$key] = array(
                    'text' => $value['subject'],
                    'condition' => str_replace("'", '"', $condition)
                );
            }
        }

        return $data;
    }

    /**
     * Get list countries 
     *
     * @return  array
     */
    public function getCountries()
    {
        return Mage::getSingleton('adminhtml/system_config_source_country')->toOptionArray();
    }
}
