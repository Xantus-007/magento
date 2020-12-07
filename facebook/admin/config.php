<?php 

include_once '../config.php' ;
include_once 'session.php' ;

include_once '../functions.php' ;

$id_config = array_key_exists('config', $_GET) ? (int) $_GET['config'] : 1 ;

if(!empty($_POST))
{
	$data = $_POST ;
	
	if(array_key_exists('image_header', $_FILES) && $_FILES['image_header']['name'])
	{
		$data['image_header'] = uploadFile($_FILES['image_header']) ;
	}
	
	if(array_key_exists('image_background', $_FILES) && $_FILES['image_background']['name'])
	{
		$data['image_background'] = uploadFile($_FILES['image_background']) ;
	}
	
	if(array_key_exists('image_nonfan', $_FILES) && $_FILES['image_nonfan']['name'])
	{
		$data['image_nonfan'] = uploadFile($_FILES['image_nonfan']) ;
	}
	
	if(array_key_exists('image_fan', $_FILES) && $_FILES['image_fan']['name'])
	{
		$data['image_fan'] = uploadFile($_FILES['image_fan']) ;
	}
	
	/*$data['regulations'] 	= $_POST['regulations'] ;
	$data['email_sender'] 	= $_POST['email_sender'] ;
	$data['email_subject'] 	= $_POST['email_subject'] ;
	$data['email_message'] 	= $_POST['email_message'] ;
	$data['register_subject'] 	= $_POST['register_subject'] ;
	$data['register_message'] 	= $_POST['register_message'] ;*/
	
	function striptags($value)
	{
		$value = strip_tags($value, '<div><a><b><i><u><br><sup>') ;
		
		return $value ;
	}
	
	$data = array_map(striptags, $data) ;
	
	$db->update(DB_PREFIX . 'configuration', $data, 'id_config = ' . $id_config) ;
	
	header('Location:board.php') ;
	exit ;
}

$config = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration WHERE id_config = ' . $id_config) ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Administration</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/screen.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="../css/admin.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="js/nicEdit.js?v=<?php echo VERSION ; ?>"></script>
<script type="text/javascript" src="js/jscolor.js?v=<?php echo VERSION ; ?>"></script>
<link href="../gfx/favicon.ico" rel="icon" type="image/x-icon" />
<style>

</style>
</head>
<body>
	<div class="container" id="main">
		<div class="span-24 last" id="content">
			<div class="content">
				<a href="board.php" style="position:absolute;top:20px;right:20px;">Retour</a>
				<h3 style="float:none;margin-bottom:1em;padding:3px 0;">Configuration 
				<select id="lang" style="padding:5px;" onchange="window.location='config.php?config=' + this.value;">
					<option value="1" <?php echo $id_config == 1 ? 'selected' : '' ;?>>Français</option>
					<option value="2" <?php echo $id_config == 2 ? 'selected' : '' ;?>>International</option>
				</select></h3>
				
				<form id="form_config" action="config.php?config=<?php echo $id_config ; ?>" method="post" enctype="multipart/form-data">
				<h4>Visuels</h4>
				<table>
					<?php /*?>
					<tr>
						<td><label for="image_background">Image de fond (810x1110)</label></td>
					</tr>
					<tr>
						<td>
							<input type="file" name="image_background" id="image_background"/>
						</td>
					</tr>
					*/?>
					<tr>
						<td><label for="image_header">Header (810x??)</label></td>
					</tr>
					<tr>
						<td>
							<input type="file" name="image_header" id="image_header"/>
						</td>
					</tr>
					<tr>
						<td><label for="image_nonfan">Image Fan Gate (450x340)</label></td>
					</tr>
					<tr>
						<td>
							<input type="file" name="image_nonfan" id="image_nonfan"/>
						</td>
					</tr>
					<tr>
						<td><label for="image_fan">Image Jeu (245x450)</label></td>
					</tr>
					<tr>
						<td>
							<input type="file" name="image_fan" id="image_fan"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="color11"><b>Couleur primaire</b> <span class="lite">utilisée sur les titres et boutons</span></label>
				   		</td>
					</tr>
					<tr>
						<td>
							  <input type="text" class="span3 color" name="color11" value="<?php echo $config->color11 ; ?>">
						</td>
					</tr>
				</table>
				<h4>Emails</h4>
				<table>
					<tr>
						<td><label for="email_sender">Expéditeur Email</label></td>
					</tr>
					<tr>
						<td><input type="text" id="email_sender" name="email_sender" value="<?php echo $config ? stripslashes($config->email_sender) : '' ; ?>"/></td>
					</tr>
				</table>
				
				<h4>Email Inscription</h4>
				<table>
					<tr>
						<td><label for="register_subject">Titre Email</label></td>
					</tr>
					<tr>
						<td><input type="text" id="register_subject" name="register_subject" value="<?php echo $config ? stripslashes($config->register_subject) : '' ; ?>"/></td>
					</tr>
					<tr>
						<td style="vertical-align:top;"><label for="register_message">Message Email</label></td>
					</tr>
					<tr>
						<td>
							<div style="float:left;height:230px;width:650px;margin-right:20px;">
								<textarea id="register_message" name="register_message" style="width:650px;"><?php echo $config ? stripslashes($config->register_message) : '' ; ?></textarea>
							</div>
							[lot] : Nom du lot<br/>
							[fname] : Prénom du gagnant<br/>
							[name] : Nom du gagnant<br/>
							[email] : Email du gagnant<br/>
							[link_parrain] : Lien bitly<br/>
						</td>
					</tr>
					<tr>
						<td>
							<input type="button" class="submit" value="Recevoir un email de test" onclick="sendTest('register');"/>
							<span style="color:#ccc;float:left;margin:10px;">l'email de test sera envoyé sur l'email expéditeur</span>
						</td>
					</tr>
				</table>
				<h4>Email Gagnant</h4>
				<table>
					<tr>
						<td><label for="email_subject">Titre Email</label></td>
					</tr>
					<tr>
						<td><input type="text" id="email_subject" name="email_subject" value="<?php echo $config ? stripslashes($config->email_subject) : '' ; ?>"/></td>
					</tr>
					<tr>
						<td style="vertical-align:top;"><label for="email_message">Message Email</label></td>
					</tr>
					<tr>
						<td>
							<div style="float:left;height:230px;width:650px;margin-right:20px;">
								<textarea id="email_message" name="email_message" style=width:650px;"><?php echo $config ? stripslashes($config->email_message) : '' ; ?></textarea>
							</div>
							[lot] : Nom du lot<br/>
							[fname] : Prénom du gagnant<br/>
							[name] : Nom du gagnant<br/>
							[email] : Email du gagnant<br/>
							</td>
					</tr>
					<tr>
						<td>
							<input type="button" class="submit" value="Recevoir un email de test" onclick="sendTest('winner');" />
							<span style="color:#ccc;float:left;margin:10px;">l'email de test sera envoyé sur l'email expéditeur</span>
						</td>
					</tr>
				</table>
				<h4>Traductions</h4>
				<table>
					<?php 
					$aTrads = array() ;
					$aTrads['text_lot'] = array('label' => 'Nom du lot (open graph)') ;
					$aTrads['text_lot_desc'] = array('label' => 'Description du lot (open graph)') ;
					$aTrads['text_share_title'] = array('label' => 'Titre du partage') ;
					$aTrads['text_share_desc'] = array('label' => 'Description du partage') ;
					$aTrads['text_disclaimer'] = array('label' => 'Diclaimer', 'textarea' => true) ;
					
					$config_fr = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration WHERE id_config = 1') ;
					$oCongif = (array) $config_fr ; 
					
					foreach($oCongif as $k => $v)
					{
						$bTextarea = false ;
						$label =  $v ? $v : $k ;
						if(substr($k, 0, 5) == 'text_')
						{
							if(array_key_exists($k, $aTrads))
							{
								$aTrad = $aTrads[$k] ;
								if(array_key_exists('label', $aTrad)) $label = $aTrad['label'] ;
								if(array_key_exists('textarea', $aTrad)) $bTextarea = true ;
							}
							
					?>
					<tr>
						<td><label for="<?php echo $k ; ?>">"<?php echo $label ; ?>"</label></td>
					</tr>
					<tr>
						<td>
							<?php if($bTextarea) { ?>
							<textarea id="<?php echo $k ; ?>" class="textfield" name="<?php echo $k ; ?>" style="width:800px;height:50px;"><?php echo str_replace('<br/>', chr(10), stripslashes($config->$k)) ; ?></textarea>
							<?php }else{ ?>
							<input type="text" id="<?php echo $k ; ?>" name="<?php echo $k ; ?>" value="<?php echo stripslashes($config->$k) ; ?>" style="width:800px;"/>
							<?php } ?>
						</td>
					</tr>
					<?php } } ?>
				</table>
				<h4>FAQ</h4>
				<table>
					<tr>
						<td>
							<textarea id="faq" name="faq" style="height:200px;width:900px;"><?php echo $config ? stripslashes($config->faq) : '' ; ?></textarea>
						</td>
					</tr>
				</table>
				<br/>
				<h4>Réglement</h4>
				<table>
					<tr>
						<td>
							<textarea id="regulations" name="regulations" style="height:200px;width:900px;"><?php echo $config ? stripslashes($config->regulations) : '' ; ?></textarea>
						</td>
					</tr>
				</table>
				<br/>				
				<table>
					<tr>
						<td>
							<input type="submit" class="submit" value="Enregistrer" />
							</td>
					</tr>
				</table>
				</form>
				<a href="board.php">Retour</a>
			</div>
		</div>
	</div>
<script type="text/javascript">
function sendTest(mode)
{
	$.post('test_email.php?mode=' + mode, $('#form_config').serialize(), function(data)
	{
		if(data == 'OK') alert('Email envoyée à ' + $('#email_sender').val()) ;
	});
}

nicEditors.allTextAreas({maxHeight:200, iconsPath : 'img/nicEditorIcons.gif', buttonList : ['bold','italic','underline']}) ;
jscolor.dir = 'img/color/' ;
jscolor.init() ;
</script>
</body>
</html>