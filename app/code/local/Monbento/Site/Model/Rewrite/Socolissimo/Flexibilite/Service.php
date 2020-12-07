<?php

class Monbento_Site_Model_Rewrite_Socolissimo_Flexibilite_Service extends Addonline_SoColissimo_Model_Flexibilite_Service
{

    /**
     * Réponds si le WS est disponible
     * @return boolean
     */
    public function isAvailable()
    {
        if (! $this->_available) {
            try {
                $supervisionUrl = "https://ws.colissimo.fr/supervision-wspudo/supervision.jsp";
                if (Mage::getStoreConfig('carriers/socolissimo/testws_socolissimo_flexibilite')) {
                    $supervisionUrl = "https://pfi.telintrans.fr/supervision-wspudo/supervision.jsp";
                }
                $ctx = stream_context_create(array(
                    'http' => array(
                        'timeout' => 1.5
                    )
                )); // Si on n'a pas de réponse en moins d'une demi seconde
                $this->_available = file_get_contents($supervisionUrl, false, $ctx);
            } catch (Exception $e) {
                $this->_available = "[KO]";
            }
        }
        return trim($this->_available) === "[OK]";
    }
}
