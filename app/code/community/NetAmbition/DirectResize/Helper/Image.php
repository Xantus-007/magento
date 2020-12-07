<?php

class NetAmbition_DirectResize_Helper_Image extends Mage_Catalog_Helper_Image
{

	public function directResize($img_min_w, $img_min_h, $ratio=0, $autoriseAgrandissement = false)
	{
		//--> Recherche des dimentions de l'image d'origine
		$size 			= getimagesize($this);
		$img_src_w 		= $size[0];
		$img_src_h 		= $size[1];
		//--> On fait le calcul de l'image que si les dimensions demandées sont inférieures à celles d'origine
		if($img_min_w < $img_src_w || $img_min_h < $img_src_h || $autoriseAgrandissement){
			//--> Calcul des dimensions de l'image à afficher suivant le ratio choisi
			switch ($ratio){
				case 0:	//-- redimensionnement aux dimentions exactes données
					$img_min_w_calc	= $img_min_w;
					$img_min_h_calc	= $img_min_h;
					break;
				case 1: //-- redimensionnement largeur fixée et hauteur calculée
					$img_min_w_calc	= $img_min_w;
					$img_min_h_calc	= round($img_src_h * $img_min_w_calc / $img_src_w);
					break;
				case 2: //-- redimensionnement hauteur fixée et largeur calculée
					$img_min_h_calc	= $img_min_h;
					$img_min_w_calc	= round($img_src_w * $img_min_h_calc / $img_src_h);
					break;
				case 3: //-- redimensionnement pour que l'image rentre proportionnellement dans la largeur et la heuteur fixées
					$ratio_wh		= $img_src_w / $img_src_h;
					$ratio_whmin	= $img_min_w / $img_min_h;
					if ($ratio_wh > $ratio_whmin){
						$img_min_w_calc	= $img_min_w;
						$img_min_h_calc	= round($img_src_h * $img_min_w_calc / $img_src_w);
					} else {
						$img_min_h_calc	= $img_min_h;
						$img_min_w_calc	= round($img_src_w * $img_min_h_calc / $img_src_h);
					}
					break;
				case 4: //-- redimensionnement pour que l'image couvre au plus juste la hauteur et la largeur fixées
					if ($img_src_w/$img_src_h > $img_min_w/$img_min_h) {
						$img_min_h_calc	= $img_min_h;
						$img_min_w_calc	= round($img_src_w * $img_min_h_calc / $img_src_h);
					} else {
						$img_min_w_calc	= $img_min_w;
						$img_min_h_calc	= round($img_src_h * $img_min_w_calc / $img_src_w);
					}
					break;
			}
			//--> On continue la procédure d'affichage de la miniature par Magento
			$this->resize($img_min_w_calc, $img_min_h_calc);
		}
		return $this;
	}
}

?>