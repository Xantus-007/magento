<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Quadra
 * @package    Quadra_Atos
 * @name        Quadra_Atos_Block_Standard_Form
 * @author      Quadra Team
 */

class Quadra_Atos_Block_Standard_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('payment/form/atos.phtml');
        parent::_construct();
    }
	
    public function getCreditCardsAccepted()
    {
        $cards = Mage::getSingleton('atos/config')->getCreditCardTypes();

        $array = array();
        foreach (explode(',', Mage::getSingleton('atos/method_standard')->getCctypes()) as $key => $value) {
            if (array_key_exists($value, $cards)) {
                $array[$value] = $cards[$value];
            }
        }

        return $array;
    }
	
    public function getAtosLogoSrc()
    {
        return $this->getUrl() . Mage::getStoreConfig('logo/atos_standard');
    }
}
