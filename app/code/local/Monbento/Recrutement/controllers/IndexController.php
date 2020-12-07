<?php

class Monbento_Recrutement_IndexController extends Mage_Core_Controller_Front_Action 
{
	public function indexAction()
	{
		//Get current layout state
		$this->loadLayout();
		 
		$block = $this->getLayout()->createBlock(
			'Mage_Core_Block_Template',
			'recrutement.form',
			array(
				'template' => 'recrutement/form.phtml'
			)
		);
		 
		$this->getLayout()->getBlock('content')->append($block);
		$this->_initLayoutMessages('core/session');
		$this->renderLayout();
	}

   	public function postAction()
	{
		$result = array();
		$result['isValid'] = false;
		$result['isUploadValid'] = true;

		//Fetch submited params
		$params = $this->getRequest()->getParams();

		if(isset($params['uploadData']))
		{
			$verifyToken = md5('unique_salt' . time());
			// Define a destination
			$targetFolder = '/media/recrutement'. '/' . $verifyToken; // Relative to the root

			if (!empty($_FILES)) 
			{
				$filesname = array('file_upload_cv', 'file_upload_lm');
				foreach ($filesname as $filename) 
				{
					$tempFile = $_FILES[$filename]['tmp_name'];
					$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
					if (!is_dir($targetPath)) mkdir($targetPath);
					$targetFile = rtrim($targetPath,'/') .  '/'. $this->_clean_file_name($_FILES[$filename]['name']);
					
					// Validate the file type
					$fileTypes = array('doc','docx','pdf','rtf','odt'); // File extensions
					$fileParts = pathinfo($_FILES[$filename]['name']);
					
					if (in_array($fileParts['extension'],$fileTypes)) 
					{
						move_uploaded_file($tempFile,$targetFile);
					} 
					else 
					{
						$result['isUploadValid'] = false;
					}
				}
			}
		}

		$body = "Civilite : ".$params['civilite']."\n";
		$body .= "Nom : ".$params['nom']."\n";
		$body .= "Prénom : ".$params['prenom']."\n";
		$body .= "Email : ".$params['email']."\n";
		$body .= "Téléphone : ".$params['telephone']."\n";
		$body .= "Fax : ".$params['fax']."\n";
		$body .= "Adresse : ".$params['adresse']."\n";
		$body .= "Complement : ".$params['complement']."\n";
		$body .= "Code Postal : ".$params['codepostal']."\n";
		$body .= "Ville : ".$params['ville']."\n";
		$body .= "Pays : ".$params['pays']."\n";
		if(isset($params['uploadData']))
		{
			$body .= "CV : ".Mage::getBaseUrl('web').$targetFolder . '/' . $this->_clean_file_name($_FILES['file_upload_cv']['name'])."\n";
			$body .= "Lettre de motivation : ".Mage::getBaseUrl('web').$targetFolder . '/' . $this->_clean_file_name($_FILES['file_upload_lm']['name'])."\n";
		}
		else
		{
			$body .= "CV : ".Mage::getBaseUrl('web').$params['cv']."\n";
			$body .= "Lettre de motivation : ".Mage::getBaseUrl('web').$params['lm']."\n";
		}
		$mail = new Zend_Mail();
		$mail->setBodyText(utf8_decode($body));
		$mail->setFrom($params['email'], $params['nom'].' '.$params['prenom']);
		$mail->addTo($params['emaildest']);
		$mail->setSubject('Nouvelle candidature - '.$params['poste']);

		if($result['isUploadValid'] == true)
		{
			try {
				$mail->send();
				$result['isValid'] = true;
			}
			catch(Exception $ex) {
				$result['message'] ='Problème lors de l\'envoi du formulaire';
			}
		}
		else
		{
			$result['message'] ='Problème lors de l\'envoi des fichiers (vérifier l\'extension)';
		}

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}

	protected function _clean_file_name($file)
	{
  		// nettoyage du nom de fichier
  		$file = strtr($file,"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
  		$file = strtolower($file);
  		$file = preg_replace("/[^a-z0-9\.\-]/i","",$file);
  		return $file;
	}
}
