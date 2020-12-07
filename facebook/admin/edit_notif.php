<?php 

include_once '../config.php' ;
include_once 'session.php' ;

include_once '../functions.php' ;

if(!empty($_POST))
{
	$id_notif = (int) $_POST['id'] ;
	
	$data = array() ;
	$data['id_group']	= $_POST['id_group'] ;
	$data['id_lot']		= $_POST['id_lot'] ;
	$data['name']		= $_POST['name'] ;
	$data['url'] 		= $_POST['url'] ;
	$data['ref'] 		= $_POST['ref'] ;
	$data['nb'] 		= $_POST['nb'] ;
	$data['text_fr'] 	= $_POST['text_fr'] ;
	$data['text_en'] 	= $_POST['text_en'] ;
	
	if($id_notif)
	{
		$db->update(DB_PREFIX . 'notifications', $data, 'id_notif = ' . $id_notif) ;
	}else{
		$id_notif = $db->insert(DB_PREFIX . 'notifications', $data) ;
		
		$id_lot 	= $data['id_lot'] ;
		$id_group 	= $data['id_group'] ;
		$nb 		= $data['nb'] ;
		
		$dataSend = array() ;
		$dataSend['id_notif']	= $id_notif ;
		$dataSend['id_group']	= $_POST['id_group'] ;
		$dataSend['id_lot']		= $_POST['id_lot'] ;
		
		$sql = 'SELECT DISTINCT id_user FROM ' . DB_PREFIX . 'participations WHERE id_lot = ' . $id_lot ;
		$sql.= ' AND id_user NOT IN (SELECT id_user FROM '. DB_PREFIX . 'users_notifications_sends WHERE id_group = ' . $id_group . ')' ;
		if($nb) $sql .= ' LIMIT 0,' . $nb ;
		echo $sql ;
		
		$users = $db->selectAll($sql) ;
		foreach($users as $u)
		{
			$dataSend['id_user'] = $u->id_user ;
			
			$db->insert(DB_PREFIX . 'users_notifications_sends', $dataSend) ;
		}
	}
	
	header('Location:notifications.php') ;
	exit ;
}

$notif = null ;
if(array_key_exists('id', $_GET))
{
	$id_notif = (int) $_GET['id'] ;
	
	$notif = $db->select('SELECT * FROM ' . DB_PREFIX . 'notifications WHERE id_notif = ' . $id_notif) ;
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
				<form action="edit_notif.php" method="post" enctype="multipart/form-data">
				<?php 
				echo '<input type="hidden" name="id" value="' . ($notif ? $notif->id_notif : 0) . '" />' ;
				?>
				<table>
					<tr>
						<td><label for="id_lot">Lot</label></td>
					</tr>
					<tr>
						<td>
							<?php if($notif)
							{
								$lot = $db->select('SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $notif->id_lot) ;
								echo '<input type="hidden" name="id_lot" value="' . $notif->id_lot . '" />' ;
								echo $lot->name_fr ;
							}else{?>
							<select id="id_lot" name="id_lot" style="padding:5px;">
							<?php 
							$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots ORDER BY id_lot DESC' ;
							$lines = $db->selectAll($sql) ;
							foreach($lines as $line)
							{
								echo '<option value="' . $line->id_lot . '" ' . ($id_lot == $line->id_lot ? 'selected' : '') . '>' . $line->name_fr . '</option>' ;
							}
								?>
							</select>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td><label for="id_group">Groupe</label></td>
					</tr>
					<tr>
						<td>
							<?php if($notif) { ?>
							Groupe <?php echo $notif->id_group ; ?>
							<input type="hidden" name="id_group" value="<?php echo $notif->id_group ; ?>" />
							<?php }else{ ?>
							<select name="id_group">
								<?php for($i = 1 ; $i <= 3 ; $i++) { ?>
								<option value="<?php echo $i ; ?>">Groupe <?php echo $i ; ?></option>
								<?php } ?>
							</select>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td><label for="name">Nom</label></td>
					</tr>
					<tr>
						<td><input type="text" id="name" name="name" value="<?php echo $notif ? stripslashes($notif->name) : '' ; ?>"/></td>
					</tr>
					<tr>
						<td><label for="url">Url</label></td>
					</tr>
					<tr>
						<td><input type="text" id="url" name="url" value="<?php echo $notif ? $notif->url : '' ; ?>"/></td>
					</tr>
					<tr>
						<td><label for="ref">Référence (pour le tracking dans les stats)</label></td>
					</tr>
					<tr>
						<td><input type="text" id="ref" name="ref" value="<?php echo $notif ? $notif->ref : '' ; ?>"/></td>
					</tr>
					<tr>
						<td><label for="text_fr">Message en français</label></td>
					</tr>
					<tr>
						<td><textarea id="text_fr" name="text_fr" style="height:70px;"><?php echo $notif ? stripslashes($notif->text_fr) : '' ; ?></textarea>
					</tr>
					<tr>
						<td><label for="text_en">Message en anglais</label></td>
					</tr>
					<tr>
						<td><textarea id="text_en" name="text_en" style="height:70px;"><?php echo $notif ? stripslashes($notif->text_en) : '' ; ?></textarea>
					</tr>
					<tr>
						<td><label for="nb">Nombre de personnes (0 = tous)</label></td>
					</tr>
					<tr>
						<td>
							<?php if($notif) {
								echo $notif->nb . ' personnes' ;
								echo '<input type="hidden" name="nb" value="' . $notif->nb . '" />' ;
							}else{?>
							<input type="text" id="nb" name="nb" value="<?php echo $notif ? $notif->nb : '' ; ?>"/>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" class="submit" value="Enregistrer" />
						</td>
					</tr>
				</table>
				</form>
				<a href="notifications.php">Retour</a>
			</div>
		</div>
	</div>
</body>
</html>