<?php
require_once 'abstract.php';

class Dbm_Shell_Update_Products_Refonte extends Mage_Shell_Abstract
{
    protected $_column = array();
    
    public function run()
    {
        $lang = $this->getArg('lang');
        $path = Mage::getBaseDir('var') . DS . 'products-update' . DS;
        $file = $path . DS . 'products-'.$lang.'-DBM.csv';
        $fp = fopen($file, 'r');
        $i = 0;
        
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        
        while($cells = fgetcsv($fp))
        {
            if($i == 0)
            {
                $columnIndex = 0;
                foreach($cells as $cellHeader)
                {
                    $this->_column[$cellHeader] = $columnIndex;
                    $columnIndex++;
                }
            }
            else
            {
                switch($lang) {
                    case 'fr':
                        $stores = array(0);
                        break;
                    case 'en':
                        $stores = array(2,9,10,6);
                        break;
                    case 'es':
                        $stores = array(4,8);
                        break;
                    case 'it':
                        $stores = array(3);
                        break;
                    case 'de':
                        $stores = array(5);
                        break;
                }
                
                foreach($stores as $storeId)
                {
                    if($product = Mage::getModel('catalog/product')->load($cells[$this->_column['ID']])->setStoreId($storeId))
                    {
                        if($product->getSku())
                        {
                            echo "START UPDATE PRODUCT ".$cells[$this->_column['ID']]. "\n";

                            $product
                                    ->setName($cells[$this->_column['Nom']])
                                    ->setBaseline($cells[$this->_column['Baseline']])
                                    ->setDisplayHome($this->_setAttributeByValue($cells[$this->_column['Afficher sur la home']]))
                                    ->setSelectForShop($this->_setAttributeByValue($cells[$this->_column['Afficher sur le shop']]))
                                    ->setCodeHexa($cells[$this->_column['Code hexa 1']])
                                    ->setCodeHexaBas($cells[$this->_column['Code hexa 2']])
                                    ->setDisplayBandeauPerso($this->_setAttributeByValue($cells[$this->_column['Afficher le bandeau de personnalisation']]))
                                    ->setTexteCaracteristiques($cells[$this->_column['Texte des caractéristiques']])
                                    ->setTexteDimensions($cells[$this->_column['Texte des dimensions/compositions']])
                                    ->setTitreDescription($cells[$this->_column['Titre pour la description']])
                                    ->setTexteBandeauPerso($cells[$this->_column['Texte du bandeau de personnalisation']])
                                    ->setTexteBandeauPersoBas($cells[$this->_column['Texte du bandeau perso sous le pinceau']]);

                            if(!empty($cells[$this->_column['Description']])) $product->setDescription($cells[$this->_column['Description']]);
                            if(!empty($cells[$this->_column['Description courte']])) $product->setShortDescription($cells[$this->_column['Description courte']]);

                            $product->getResource()->saveAttribute($product, 'name'); 
                            $product->getResource()->saveAttribute($product, 'baseline'); 
                            $product->getResource()->saveAttribute($product, 'display_home'); 
                            $product->getResource()->saveAttribute($product, 'select_for_shop');
                            $product->getResource()->saveAttribute($product, 'code_hexa');
                            $product->getResource()->saveAttribute($product, 'code_hexa_bas');
                            $product->getResource()->saveAttribute($product, 'display_bandeau_perso');
                            $product->getResource()->saveAttribute($product, 'texte_caracteristiques');
                            $product->getResource()->saveAttribute($product, 'texte_dimensions');
                            $product->getResource()->saveAttribute($product, 'titre_description');
                            $product->getResource()->saveAttribute($product, 'texte_bandeau_perso');
                            $product->getResource()->saveAttribute($product, 'texte_bandeau_perso_bas');

                            if(!empty($cells[$this->_column['Description']])) $product->getResource()->saveAttribute($product, 'description');
                            if(!empty($cells[$this->_column['Description']])) $product->getResource()->saveAttribute($product, 'short_description');

                            echo "END UPDATE PRODUCT ".$product->getSku(). "\n";
                        }
                    }
                }
            }
            $i++;
        }
        fclose($fp);
        
        echo '<pre>END</pre>';
        exit();
    }
    
    protected function _setAttributeByValue($value, $type = 'boolean', $attributeCode = null)
    {
        $value = trim($value);
        switch($type)
        {
            case 'boolean':
                return ($value == '1') ? 1 : 0;
                break;
            case 'select':
                return Mage::helper('dbm_utils/product')->getAttributeOptionIdByLabel($attributeCode, $value);
                break;
            case 'multiselect':
                return array(Mage::helper('dbm_utils/product')->getAttributeOptionIdByLabel($attributeCode, $value));
                break;
        }
        
        return '';
    }
}

$shell = new Dbm_Shell_Update_Products_Refonte();
$shell->run();
