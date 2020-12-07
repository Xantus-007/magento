<?php

class sendMail{

	var $enTete = array();
	var $dest_str = null;
	var $retourMail = null;
	var $expediteur = null;
	var $sujet = null;
	var $message = null;
	var $messageHtml = null;
	var $messageText = null;
	var $limite = "____________________________Next";

	/**
	Constructeur
	*/
	function __construct()
	{
		//ini_set('sendmail_path', "/usr/sbin/sendmail -t -i -fnoreply@noreply.fr");
	}


	/**
	fonction destMail
	@param	dest	STRING ou ARRAY
	*/
	function destMail($dest)
	{
		settype($destOk , "array");
		if(is_array($dest)){
			$dest = array_unique($dest);
		}
		else{
			$dest=str_replace(" ","",$dest);
			$dest=explode("\r\n", $dest);
			$dest = array_unique($dest);
		}
		// les destinatires sont remis en chane de caractre du type : mail@mail.com,mail@mail.com,mail@mail.com
		foreach ($dest as $myDest){
			if($this->verifMail($myDest)){
				array_push($destOk, $myDest);
			}
		}
		$this->dest_str= implode(",",$dest);
	}


	/**
	 * Fonction verifMail
	 *
	 * @param string $mailAverif
	 */
	function verifMail($mailAverif)
	{
		if(preg_match("/([A-Za-z0-9]|-|_|\.)*@([A-Za-z0-9]|-|_|\.)*\.([A-Za-z0-9]|-|_|\.)*/",$mailAverif)) {
			return true;
		}else {
			return false;
		}
	}

	/**
	 *Fonction headMail
	 *
	 * @param 	from	String
	 * @param 	reply	String
	 *
	 */
	function headMail($from,$reply)
	{
		//expditeur du mail
		$this->enTete[] = 'From:'.$from;
		
		//adresse de rponse du mail
		$this->retourMail=$reply;
		$this->enTete[] = 'Reply-To:'.$reply;
		
		//version Mime du mail
		$this->enTete[] = 'MIME-Version: 1.0' ;
		$this->enTete[] = "Content-Type: text/html;\n\tcharset=\"utf-8\"\n\n\r\n\r\n";
		$this->enTete = implode("\n",$this->enTete);
	}

	/**
	 * Fonction contenuMail
	 *
	 * @param string $mailHtml
	 */
	//--fonction qui gre le contenu du mail
	function contenuMail($mailHtml){

		//--traitement des chaines de caractres
		/*
		$mailHtml = str_replace("'","\'",$mailHtml);
		$mailHtml = str_replace("\\\"","\"",$mailHtml);
		*/

		//--mail HTML trait
		$this->messageHtml=$mailHtml;

		//--message text Plein
		//--on enlve les balises Html sauf le retourMails  la ligne
		$mailText=strip_tags($mailHtml,"<br><br /></title>");

		//--on dcode les caractres spciaux Html
		$mailText=html_entity_decode($mailText);

		//--on remplace les retourMails  la ligne par des retourMails chariots.
		$mailText=str_replace("<br>","\n",$mailText);
		$mailText=str_replace("<br />","\n",$mailText);
		$mailText=str_replace("</title>","\n",$mailText);

		//--message text trait
		$this->messageText=$mailText;
		$this->constructionMail();
	}

	/**
	 * Fonction constructionMail
	 *
	 */
	//--fonction de construction du message
	function constructionMail()
	{
		$texte_simple = "";
		/*$texte_simple = "--".$this->limite."\n";
		$texte_simple .= "Content-Type: text/plain; charset=iso-8859-1\nContent-Transfer-Encoding: 7bit\n\r\n\r\n";
		$texte_simple .=$this->messageText;
		$texte_simple .= "\n\n";*/
		
		//--le message en html original
		$texte_html = $this->messageHtml;
		/*$texte_html = "--".$this->limite."\n";
		$texte_html .= "Content-Type: text/html; charset=iso-8859-1\nContent-Transfer-Encoding: 7bit\n\r\n\r\n";
		$texte_html .= $this->messageHtml;
		$texte_html .= "\n\n\n--".$this->limite."--";*/
		
		//--concatnation des deux textes
		$this->message = $texte_simple.$texte_html;
	}

	/**
	 * Fonction sujetTraitement
	 *
	 * @param string $sujet
	 */
	//--fonction de gestion et de mise en forme du titre
	function sujetTraitement($sujet)
	{
		//--premire lettre du sujet est passe en majuscule
		$sujet = ucfirst($sujet);
		//$this->sujet = html_entity_decode($sujet);
		$this->sujet = $sujet;
	}

	/**
	 * Fonction envoi
	 *
	 * @param string $mailHtml
	 * @param string $sujet
	 */
	//--fonction qui permet d'envoyer l'email
	function envoi($mailHtml,$sujet)
	{
		//--appel de la fonction avec en parametre le texte html du message - sans cration de fichire temporaire
		$this->contenuMail($mailHtml,"");
		$this->sujetTraitement($sujet);
		
		/*
		echo "Dest : ".$this->dest_str."<br>";
		echo "sujet : ".$this->sujet."<br>";
		echo "message : ".$this->message."<br>";
		echo "enTete : ".$this->enTete."<br>";
		echo "enTete : ".$this->retourMail."<br>";
		*/
		
		//--envoi de l email
		if(!mail($this->dest_str,$this->sujet,$this->message,$this->enTete))
		{
			return false;
		}
		
		else return true;
	}
}
?>
