<?php 

include_once '../config.php' ;
include_once 'session.php' ;

include_once '../functions.php' ;

if(!empty($_POST))
{
	$id_lot = (int) $_POST['id'] ;
	
	$nb = $_POST['nb'] ? $_POST['nb'] : 0 ;
	$fq = $_POST['frequency'] ? $_POST['frequency'] : 0 ;
		
	if($id_lot)
	{
		$sql = 'UPDATE ' . DB_PREFIX . 'lots SET name = "' . addslashes($_POST['name']) . '", value = "' . addslashes($_POST['value']) . '", nb = ' . $nb . ', frequency = ' . $fq . ' WHERE id_lot = ' . $id_lot ; 
		$result = mysql_query($sql);
	}else{
		$sql = 'INSERT INTO ' . DB_PREFIX . 'lots VALUES ("", "' . addslashes($_POST['name']) . '", "' . addslashes($_POST['value']) . '", ' . $fq . ', ' . $nb . ', 0, NULL, NULL, 0);' ; 
		$result = mysql_query($sql);
		$id_lot = mysql_insert_id($link) ;
	}
	
	if(!empty($_FILES) && $_FILES['image']['tmp_name'])
	{
		$path = dirname(dirname(__FILE__)) . '/lots/' ; 
		
		move_uploaded_file($_FILES['image']['tmp_name'], $path . $id_lot . '.jpg') ;
	}
	
	header('Location:board.php') ;
	exit ;
}

if(array_key_exists('del', $_GET))
{
	$id_lot = (int) $_GET['del'] ;
	
	if($id_lot)
	{
		$sql = 'UPDATE ' . DB_PREFIX . 'lots SET state = -1 WHERE id_lot = ' . $id_lot ;
		mysql_query($sql) ;
		
		header('Location:board.php') ;
		exit ;
	}
}

$lot = null ;
if(array_key_exists('id', $_GET))
{
	$id_lot = (int) $_GET['id'] ;
	
	$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $id_lot ; 
	$result = mysql_query($sql);
	$lot = mysql_fetch_object($result) ;
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
						<td><label for="name">Nom</label></td>
					</tr>
					<tr>
						<td><input type="text" id="name" name="name" value="<?php echo $lot ? stripslashes($lot->name) : '' ; ?>"/></td>
					</tr>
					<tr>
						<td><label for="value">Valeur d'un lot</label></td>
					</tr>
					<tr>
						<td><input style="width:50px;" type="text" id="value" name="value" value="<?php echo $lot ? stripslashes($lot->value) : '' ; ?>"/> euros TTC</td>
					</tr>
					<tr>
						<td><label for="image">Image (175x175)</label></td>
					</tr>
					<tr>
						<td>
							<input type="file" name="image" id="image"/>
						</td>
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