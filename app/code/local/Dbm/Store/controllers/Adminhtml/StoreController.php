<?php

class Dbm_Store_Adminhtml_StoreController extends Mage_Adminhtml_Controller_Action
{
    public function _construct()
    {
        parent::_construct();
        
        $this->_publicActions[] = 'import';
    }
    
    public function importAction()
    {
        $filePath = Mage::getBaseDir('var').DS.'import-stores'.DS.'import.csv';

        $collection = Mage::getModel('dbm_store/location')->getCollection();
        foreach($collection as $store)
        {
            $store->delete();
        }
        
        $fp = fopen($filePath, 'r');
        $i = 0;
        while($line = fgetcsv($fp, 0, ';'))
        {
            if($i > 0 && strlen(trim($line[0])))
            {
                
                $address = $line[1]." \r\n".$line[2].' '.$line[3].', '.$line[4];

                if(strpos($line[6], '°'))
                {
                    $lat = explode('°', $line[6]);
                    $line[6] = $lat[0].'.'.preg_replace('/\D/', '', $lat[1]);
                }

                if(strpos($line[7], '°'))
                {
                    $lng = explode('°', $line[7]);
                    $line[7] = $lng[0].'.'.preg_replace('/\D/', '', $lng[1]);
                }
                
                /*
                $line[6] = sprintf('%0.10f', $line[6]);
                $line[7] = sprintf('%0.10f', $line[7]);
                */
                
                $data = array(
                    'title' => $line[0],
                    'address' => $address,
                    'address_display' => $address,
                    'phone' => $line[5],
                    'latitude' => $line[6],
                    'longitude' => $line[7],
                );
                
                $model = Mage::getModel('dbm_store/location');
                $model->addData($data);

                if(empty($line[6]) || empty($line[6]))
                {
                    $model->fetchCoordinates();
                }

                $model->save();
            }
            
            $i++;
        }
        
        fclose($fp);
        
        echo '<pre>END</pre>';
        exit();
    }
}