<?php 

include_once '../config.php' ;
include_once 'session.php' ;

include_once '../functions.php' ;


if(!empty($_POST))
{
	$data = array() ;
	
	if(!empty($_FILES) && $_FILES['image']['tmp_name'])
	{
		$path = dirname(dirname(__FILE__)) . '/lots/' ; 
		
		$file = uniqid('front_') ;
		
		move_uploaded_file($_FILES['image']['tmp_name'], $path . $file . '.jpg') ;
		
		$data['front'] = $file . '.jpg' ;
	}
	
	$data['regulations'] 	= $_POST['regulations'] ;
	$data['email_sender'] 	= $_POST['email_sender'] ;
	$data['email_subject'] 	= $_POST['email_subject'] ;
	$data['email_message'] 	= $_POST['email_message'] ;
	
	$db->update(DB_PREFIX . 'configuration', $data, 'id_config = 1') ;
	
	header('Location:board.php') ;
	exit ;
}

$config = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration') ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Administration</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/screen.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="../css/admin.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<link href="../gfx/favicon.ico" rel="icon" type="image/x-icon" />
</head>
<body>
	<div class="container" id="main">
		<div class="span-24 last" id="content">
			<div class="content">
				<h3 style="float:none;margin-bottom:1em;padding:3px 0;">Configuration</h3>
				<form id="form_config" action="config.php" method="post" enctype="multipart/form-data">
				<h4>Visuels</h4>
				<table>
					<tr>
						<td><label for="image">Image de démarrage (500x350)</label></td>
					</tr>
					<tr>
						<td>
							<input type="file" name="image" id="image"/>
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
				<h4>Email Gagnant</h4>
				<table>
					<tr>
						<td><label for="email_sender">Expéditeur Email</label></td>
					</tr>
					<tr>
						<td><input type="text" id="email_sender" name="email_sender" value="<?php echo $config ? stripslashes($config->email_sender) : '' ; ?>"/></td>
					</tr>
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
							<textarea id="email_message" name="email_message" style="float:left;height:200px;width:650px;margin-right:10px;"><?php echo $config ? stripslashes($config->email_message) : '' ; ?></textarea>
							[lot] : Nom du lot<br/>
							[fname] : Prénom du gagnant<br/>
							[name] : Nom du gagnant<br/>
							[email] : Email du gagnant<br/>
							</td>
					</tr>
					<tr>
						<td>
							<input type="submit" class="submit" value="Enregistrer" />
							<input type="button" class="submit" value="Recevoir un email de test" onclick="sendTest();" style="margin-left:10px;"/>
						</td>
					</tr>
					<tr>
						<td><span style="color:#ccc;">l'email de test sera envoyé sur l'email expéditeur</span></td>
					</tr>
				</table>
				</form>
				<a href="board.php">Retour</a>
			</div>
		</div>
	</div>
<script type="text/javascript">
function sendTest()
{
	$.post('test_email.php', $('#form_config').serialize(), function(data)
	{
		if(data == 'OK') alert('Email envoyée à ' + $('#email_sender').val()) ;
	});
}
</script>
</body>
</html>