<?php
/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Addonline_SoColissimo
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_AjaxController extends Mage_Core_Controller_Front_Action
{

    /**
     * Load liste relais
     */
    public function selectorAction()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('socolissimo_ajax_selector');
        $layout->generateXml();
        $layout->generateBlocks();
        $this->renderLayout();
        return $this;
    }

    /**
     * Load liste relais
     */
    public function listRelaisAction()
    {
        $poste = $this->getRequest()->getParam('poste', false);
        $cityssimo = $this->getRequest()->getParam('cityssimo', false);
        $commercant = $this->getRequest()->getParam('commercant', false);
        $country = $this->getRequest()->getParam('country', false);
        
        $typesRelais = array();
        $optInternational = Mage::getStoreConfig('carriers/socolissimo/international');
        if ($poste == 'true') {
            if ($country === 'FR' || $optInternational === '1') {
                $typesRelais[] = 'BPR';
                $typesRelais[] = 'CDI';
                $typesRelais[] = 'ACP';
            }
            if ($country === 'BE' || $optInternational === '1') {
                $typesRelais[] = 'BDP';
            }
        }
        if ($cityssimo == 'true') {
            if ($country === 'FR' || $optInternational === '1') {
                $typesRelais[] = 'CIT';
            }
        }
        if ($commercant == 'true') {
            if ($country === 'FR' || $optInternational === '1') {
                $typesRelais[] = 'A2P';
            }
            if ($country === 'BE' || $optInternational === '1') {
                $typesRelais[] = 'CMT';
            }
        }
        
        if (Mage::helper('socolissimo')->isFlexibilite()) {
            
            $adresse = urlencode($this->getRequest()->getParam('adresse', false));
            $zipcode = urlencode($this->getRequest()->getParam('zipcode', false));
            $ville = urlencode($this->getRequest()->getParam('ville', false));
            
            // le filtre du WS permet seulement d'exclure les commerçants : on filtre les résultats après l'appel au WS */
            $filterRelay = 0;
            if ($commercant == 'true' || $commercant === 'checked') {
                $filterRelay = 1;
            }
            
            $listrelais = Mage::getSingleton('socolissimo/flexibilite_service')->findRDVPointRetraitAcheminement($adresse, $zipcode, $ville, $country, $filterRelay);
            if ($listrelais->errorCode == 0) {
                if (isset($listrelais->listePointRetraitAcheminement) && is_array($listrelais->listePointRetraitAcheminement)) {
                    $itemsObject = array();
                    $itemsArray = array();
                    foreach ($listrelais->listePointRetraitAcheminement as $pointRetraitAcheminement) {
                        if (in_array($pointRetraitAcheminement->typeDePoint, $typesRelais)) {
                            $relais = Mage::getModel('socolissimo/flexibilite_relais');
                            $relais->setPointRetraitAcheminement($pointRetraitAcheminement);
                            $relais->setData('urlPicto', Mage::getDesign()->getSkinUrl("images/socolissimo/picto_" . $relais->getType() . ".png"));
                            $itemsObject[] = $relais;
                            $itemsArray[] = $relais->getData();
                        }
                    }
                    $result['items'] = $itemsArray;
                    $result['html'] = $this->_getListRelaisHtml($itemsObject);
                } else {
                    $result['error'] = 'Aucun point de livraison trouvé';
                    $result['items'] = array();
                    $result['html'] = '';
                }
            } else {
                $result['error'] = $listrelais->errorMessage;
            }
        } else {
            
            $latitude = $this->getRequest()->getParam('latitude', false);
            $longitude = $this->getRequest()->getParam('longitude', false);
            $weight = Mage::helper('socolissimo')->getQuoteWeight() / 1000; // poids en kg dans la base liberte
            
            $listrelais = Mage::getModel('socolissimo/liberte_relais')->getCollection();
            $listrelais->prepareNearestByType($latitude, $longitude, $typesRelais, $weight);
            
            foreach ($listrelais as $relais) {
                $relais->setData('urlPicto', Mage::getDesign()->getSkinUrl("images/socolissimo/picto_" . $relais->getType() . ".png"));
                $relais->getType(); // set the value
                $relais->isParking(); // set the value
                $relais->isManutention(); // set the value
                $listFermetures = Mage::getModel('socolissimo/liberte_periodesFermeture')->getCollection();
                $listFermetures->addFieldToFilter('id_relais_fe', $relais->getId());
                $relais->setData('fermetures', $listFermetures->toArray());
            }
            $result = $listrelais->toArray(); // Pas besoin de spécifier le clé 'items', l'objet array porte déjà ses élélments sur 'items'
            $result['html'] = $this->_getListRelaisHtml($listrelais);
            $result['skinUrl'] = Mage::getDesign()->getSkinUrl("images/socolissimo/");
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    /**
     * Liste les villes d'un code postal donné
     */
    public function getcitynamebyzipcodeAction()
    {
        $zipcode = $this->getRequest()->getParam('zipcode', false);
        $country = $this->getRequest()->getParam('country', false);
        
        $this->_connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $sqlResults = $this->_connectionRead->fetchAll("SELECT city_name as placeName FROM " . Mage::getSingleton('core/resource')->getTableName('socolissimo_cities') . " WHERE country = '" . $country . "' and city_zipcode = '" . $zipcode . "';");
        if ($sqlResults) {
            $results = array();
            $results['postalCodes'] = $sqlResults;
            echo 'reloadCities(' . json_encode($results) . ');';
        } else {
            echo false;
        }
    }

    /**
     * Affiche la liste de relais
     * @param unknown $list
     * @return string
     */
    protected function _getListRelaisHtml($list)
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('socolissimo_ajax_listrelais');
        $layout->generateXml();
        $layout->generateBlocks();
        $layout->getBlock('root')->setListRelais($list);
        $output = $layout->getOutput();
        return $output;
    }
}
