<?php 

include_once '../config.php' ;
include_once 'session.php' ;

include_once '../functions.php' ;

if(!empty($_POST))
{
	$id_lot = (int) $_POST['id'] ;
	
	$data = array() ;
	$data['name_fr']	= $_POST['name_fr'] ;
	$data['name_en']	= $_POST['name_en'] ;
	$data['value'] 		= $_POST['value'] ;
	$data['nb'] 		= (int) $_POST['nb'] ;
	$data['frequency'] 	= (int) $_POST['frequency'] ;
	$data['exclude_countries'] 	= $_POST['exclude_countries'] ;
	$data['option_kids'] = array_key_exists('option_kids', $_POST) ? 1 : 0 ;
	
	if($id_lot)
	{
		$db->update(DB_PREFIX . 'lots', $data, 'id_lot = ' . $id_lot) ;
	}else{
		$id_lot = $db->insert(DB_PREFIX . 'lots', $data) ;
	}
	
	if(!empty($_FILES) && $_FILES['image_fr']['tmp_name'])
	{
		$path = dirname(dirname(__FILE__)) . '/lots/' ; 
		
		$file = uniqid() . '.jpg' ;
		move_uploaded_file($_FILES['image_fr']['tmp_name'], $path . $file) ;
		
		$db->update(DB_PREFIX . 'lots', array('image_fr' => $file), 'id_lot = ' . $id_lot) ;
	}
	
	if(!empty($_FILES) && $_FILES['image_en']['tmp_name'])
	{
		$path = dirname(dirname(__FILE__)) . '/lots/' ;
	
		$file = uniqid() . '.jpg' ;
		move_uploaded_file($_FILES['image_en']['tmp_name'], $path . $file) ;
	
		$db->update(DB_PREFIX . 'lots', array('image_en' => $file), 'id_lot = ' . $id_lot) ;
	}
	
	header('Location:board.php') ;
	exit ;
}

if(array_key_exists('del', $_GET))
{
	$id_lot = (int) $_GET['del'] ;
	
	if($id_lot)
	{
		$db->update(DB_PREFIX . 'lots', array('state' => -1), 'id_lot = ' . $id_lot) ;
		
		header('Location:board.php') ;
		exit ;
	}
}

$lot = null ;
if(array_key_exists('id', $_GET))
{
	$id_lot = (int) $_GET['id'] ;
	
	$lot = $db->select('SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $id_lot) ;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Administration</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/screen.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="../css/admin.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
<link href="../gfx/favicon.ico" rel="icon" type="image/x-icon" />
</head>
<body>
	<div class="container" id="main">
		<div class="span-24 last" id="content">
			<div class="content">
				<h3 style="float:none;margin-bottom:1em;padding:3px 0;">Lot</h3>
				<form action="edit.php" method="post" enctype="multipart/form-data">
				<?php 
				echo '<input type="hidden" name="id" value="' . ($lot ? $lot->id_lot : 0) . '">' ;
				?>
				<table>
					<tr>
						<td><label for="name_fr">Nom du lot en français</label></td>
					</tr>
					<tr>
						<td><input type="text" id="name_fr" name="name_fr" value="<?php echo $lot ? stripslashes($lot->name_fr) : '' ; ?>"/></td>
					</tr>
					<tr>
						<td><label for="name_en">Nom du lot en anglais</label></td>
					</tr>
					<tr>
						<td><input type="text" id="name_en" name="name_en" value="<?php echo $lot ? stripslashes($lot->name_en) : '' ; ?>"/></td>
					</tr>
					<tr>
						<td><label for="nb">Nombre de lots</label></td>
					</tr>
					<tr>
						<td><input style="width:50px;" type="text" id="nb" name="nb" value="<?php echo $lot ? $lot->nb : '10' ; ?>"/></td>
					</tr>
					<tr>
						<td><label for="frequency">Fréquence de gains</label></td>
					</tr>
					<tr>
						<td><input style="width:50px;" type="text" id="frequency" name="frequency" value="<?php echo $lot ? $lot->frequency  : '100' ; ?>"/></td>
					</tr>
					<tr>
						<td><label for="exclude_countries">Liste des pays à excludre (code ISO séparé par des virgules Ex : US,CA)</label></td>
					</tr>
					<tr>
						<td><input type="text" id="exclude_countries" name="exclude_countries" value="<?php echo $lot ? $lot->exclude_countries  : '' ; ?>"/></td>
					</tr>
					<tr>
						<td><label for="option_kids">Option "Kids"</label></td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" id="option_kids" name="option_kids" value="1" <?php echo $lot && $lot->option_kids ? 'checked' : '' ; ?> />
							<label for="option_kids">Activer l'option</label>
						</td>
					</tr>
					<tr>
						<td><label for="image_fr">Image de partage française du lot</label></td>
					</tr>
					<tr>
						<td>
							<input type="file" name="image_fr" id="image_fr"/>
						</td>
					</tr>
					<tr>
						<td><label for="image_en">Image de partage anglaise du lot</label></td>
					</tr>
					<tr>
						<td>
							<input type="file" name="image_en" id="image_en"/>
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" class="submit" value="Enregistrer" />
						</td>
					</tr>
				</table>
				</form>
				<?php
				if($lot)
				{
					echo '<a href="edit.php?del=' . $lot->id_lot . '">Supprimer le lot</a><br/>' ;
				}
				?>
				<a href="board.php">Retour</a>
			</div>
		</div>
	</div>
</body>
</html>