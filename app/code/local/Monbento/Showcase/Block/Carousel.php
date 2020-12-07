<?php

class Monbento_Showcase_Block_Carousel extends Mage_Core_Block_Template
{
		public function getCarousel($filter)
		{
				$input = Mage::getStoreConfig($filter);
				foreach($input as $k => $v) {
						if (isset($v['image']) && !empty($v['image'])) $output[] = $v;
				}

				//die(var_dump($output));
				return $output;
		}
}