<?php

class Monbento_Site_Block_Rewrite_Unirgy_Giftcert_Product_Type extends Unirgy_Giftcert_Block_Product_Type
{
    
    protected function _getDropDownAmountHtml($config)
    {

        $label = $this->__('Select Amount:');
        $html  = sprintf('<label for="amount" class="required"><em>*</em> %s</label><div class="input-box">
            <select id="amount" name="amount" class="select required-entry">', $label);

        foreach ($config['options'] as $_value):
            $selected    = isset($config['currentAmount']) && $config['currentAmount'] == $_value ? 'selected="selected"' : '';
            $_valueLabel = !$_value ? $this->__('Please select') : $this->getCurrency()->format($_value);
            $html .= sprintf('<option value="%s" %s>%s</option>', $_value, $selected, $_valueLabel);
        endforeach;

        $html .= '</select></div>';
        return $html;
    }

}
