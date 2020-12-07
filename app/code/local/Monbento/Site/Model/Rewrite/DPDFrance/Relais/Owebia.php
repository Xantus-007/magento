<?php

class Monbento_Site_Model_Rewrite_DPDFrance_Relais_Owebia extends DPDFrance_Relais_Model_Owebia 
{

    protected function _parseInput() 
    {
        $config_string = str_replace(
                array('&gt;', '&lt;', '“', '”', utf8_encode(chr(147)), utf8_encode(chr(148)), '&laquo;', '&raquo;', "\r\n"), array('>', '<', '"', '"', '"', '"', '"', '"', "\n"), $this->_input
        );

        if (substr($config_string, 0, 2) == '$$')
            $config_string = $this->uncompress(substr($config_string, 2, strlen($config_string)));

        $config = self::json_decode($config_string);
        $config = (array) $config;

        $this->_config = array();
        $available_keys = array('type', 'label', 'enabled', 'description', 'fees', 'conditions', 'shipto', 'origin', 'customer_groups');
        $reserved_keys = array('*code');

        $deprecated_properties = array();
        $unknown_properties = array();

        foreach ($config as $code => $object) {
            $object = (array) $object;
            $row = array();
            $i = 1;
            foreach ($object as $property_name => $property_value) {
                if (in_array($property_name, $reserved_keys))
                    continue;
                if (in_array($property_name, $available_keys) || substr($property_name, 0, 1) == '_' || in_array($object['type'], array('data', 'meta'))) {
                    if (isset($property_value))
                        $row[$property_name] = array('value' => $property_value, 'original_value' => $property_value);
                }
                else
                if (!in_array($property_name, $unknown_properties))
                    $unknown_properties[] = $property_name;
                $i++;
            }
            $this->_addRow($row);
        }
        $row = null;
        if (count($unknown_properties) > 0)
            $this->addMessage('error', $row, null, 'Usage of unknown properties %s', ': <span class=osh-key>' . implode('</span>, <span class=osh-key>', $unknown_properties) . '</span>');
        if (count($deprecated_properties) > 0)
            $this->addMessage('warning', $row, null, 'Usage of deprecated properties %s', ': <span class=osh-key>' . implode('</span>, <span class=osh-key>', $deprecated_properties) . '</span>');
    }

}

?>
