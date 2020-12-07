<?php

require_once('abstract.php');

class Dbm_Shell_ImportTvaUs extends Mage_Shell_Abstract
{
    public function run()
    {
        if($this->getArg('file') && $this->getArg('region')) {
            $filePath = Mage::getBaseDir('var').DIRECTORY_SEPARATOR.'import-tva'.DIRECTORY_SEPARATOR;
            $file = $this->getArg('file');

            $file = $filePath . $file;
            $fp = fopen($file, 'r');
            $i = 0;

            echo 'START IMPORT TAX'."\n";
            $regionId = $this->getArg('region');

            while($cells = fgetcsv($fp))
            {
                if($i == 0)
                {
                    $x = 0; foreach ($cells as $cell)
                    {
                        $headers[$cell] = $x;
                        $x++;
                    }
                }
                else
                {
                    $taxCalculationRate = null;
                    $taxCalculationRate = Mage::getModel('tax/calculation_rate');
                    $dataRate = array(
                        'tax_country_id' => 'US',
                        'tax_region_id'  => $regionId,
                        'tax_postcode' => $cells[$headers['ZipCode']],
                        'code' => 'TVA-US-'.$regionId.'-'.$cells[$headers['ZipCode']],
                        'rate' => (float) str_replace(array('%', ','), array('', '.'), $cells[$headers['EstimatedCombinedRate']])
                    );

                    $taxCalculationRate->addData($dataRate)->save();

                    $taxCalculation = null;
                    $taxCalculation = Mage::getModel('tax/calculation');
                    $dataCalculation = array(
                        'tax_calculation_rate_id' => $taxCalculationRate->getTaxCalculationRateId(),
                        'tax_calculation_rule_id' => 2,
                        'customer_tax_class_id' => 3,
                        'product_tax_class_id' => 2
                    );
                    $taxCalculation->addData($dataCalculation)->save();

                    echo 'IMPORT ZIPCODE '.$cells[$headers['ZipCode']].' WITH RATE '.(float) str_replace(array('%', ','), array('', '.'), $cells[$headers['EstimatedCombinedRate']])."\n";
                }
                $i++;
            }
        }

        echo 'END'."\n";
        exit();
    }
}


$shell = new Dbm_Shell_ImportTvaUs();
$shell->run();
