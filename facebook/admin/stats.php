<?php 
include_once '../config.php' ;
include_once 'session.php' ;
include_once '../functions.php' ;

$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots ORDER BY id_lot DESC' ;
$lot 	= $db->select($sql) ;

$id_lot = array_key_exists('id_lot', $_GET) ? (int) $_GET['id_lot'] : $lot->id_lot ;

$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $id_lot ;
$lot 	= $db->select($sql) ;
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
				<h3 style="float:none;margin-bottom:1em;padding:3px 0;">Statistiques
				<select id="id_lot" style="padding:5px;" onchange="window.location='stats.php?id_lot=' + this.value;">
					<?php 
					$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots' ;
					$lines = $db->selectAll($sql) ;
					echo '<option value="0">Tous les lots</option>' ;
					foreach($lines as $line)
					{
						echo '<option value="' . $line->id_lot . '" ' . ($id_lot == $line->id_lot ? 'selected' : '') . '>' . $line->name_fr . '</option>' ;
					}
					?>
				</select></h3>
				<table>
					<tr>
						<td>Nombre de participants</td>
						<td>
						<?php 						
						$sql = 'SELECT COUNT(DISTINCT id_user) nb FROM ' . DB_PREFIX . 'participations' ;
						if($id_lot) $sql .= ' WHERE id_lot = ' . $id_lot ;
						$count = $db->select($sql) ;
						$nbUsers = $count->nb ;
						echo '<b>' . $nbUsers . '</b>' ;
						?>
						</td>
					</tr>
					<tr>
						<td>Nombre de participations</td>
						<td>
						<?php 
						$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'participations' ;
						if($id_lot) $sql .= ' WHERE id_lot = ' . $id_lot ;
						$count = $db->select($sql) ;
						$nbParticipations = $count->nb ;
						echo '<b>' . $nbParticipations . '</b>' ;
						?>
						</td>
					</tr>
					<tr>
						<td>Moyenne de participation</td>
						<td>
						<?php 
						echo '<b>' . $nbUsers ? round($nbParticipations / $nbUsers, 2) : 0 . '</b>' ;
						?>
						</td>
					</tr>
					<?php if($lot && $lot->option_kids) { ?>
					<tr>
						<td>Sélection Pastilles Enfant</td>
						<td>
						<?php 
						$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'users_kidsavis' ;
						$count = $db->select($sql) ;
						$nbAvis = $count->nb ;
						echo '<b>' . $nbAvis . ' (' . round($nbAvis / $nbUsers * 100) . '%)</b>' ;
						?>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td>Taux via parrainage</td>
						<td>
						<?php 
						$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'participations WHERE id_inv IS NOT NULL' ;
						if($id_lot) $sql .= ' AND id_lot = ' . $id_lot ;
						$count = $db->select($sql) ;
						echo '<b>' . round($count->nb / $nbParticipations * 100) . '%</b>' ;
						?>
						</td>
					</tr>
					<tr><td></td></tr>
					<tr>
						<td>Inscription Optin 1</td>
						<td>
						<?php 
						$sql = 'SELECT COUNT(DISTINCT id_user) nb FROM ' . DB_PREFIX . 'users' ;
						if($id_lot) $sql .= ' WHERE date >= "' . $lot->date_start . '"' . ($lot->date_end ? (' AND date <="' . $lot->date_end . '"') : '') ;
						$count = $db->select($sql) ;
						$nbUsers = $count->nb ;
						
						$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'users WHERE optin1 = 1' ;
						if($id_lot) $sql .= ' AND date >= "' . $lot->date_start . '"' . ($lot->date_end ? (' AND date <="' . $lot->date_end . '"') : '') ;
						$count = $db->select($sql) ;
						echo '<b>' . round($count->nb / $nbUsers * 100) . '%</b>' ;
						?>
						</td>
					</tr>
					<tr>
						<td>Inscription Optin 2</td>
						<td>
						<?php 
						$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'users WHERE optin2 = 1' ;
						if($id_lot) $sql .= ' AND date >= "' . $lot->date_start . '"' . ($lot->date_end ? (' AND date <="' . $lot->date_end . '"') : '') ;
						$count = $db->select($sql) ;
						echo '<b>' . round($count->nb / $nbUsers * 100) . '%</b>' ;
						?>
						</td>
					</tr>
					<tr><td></td></tr>
					<tr>
						<td>Nombre d'invitations envoyées</td>
						<td>
						<?php 
						$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'users_invitations_sends' ;
						if($id_lot) $sql .= ' WHERE date >= "' . $lot->date_start . '"' . ($lot->date_end ? (' AND date <="' . $lot->date_end . '"') : '') ;
						$count = $db->select($sql) ;
						$nbInvitations = $count->nb ;
						echo '<b>' . $nbInvitations . '</b>' ;
						?>
						</td>
					</tr>
					<tr>
						<td>Nombre d'invitations cliquées</td>
						<td>
						<?php 
						$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'users_invitations_clics' ;
						if($id_lot) $sql .= ' WHERE date >= "' . $lot->date_start . '"' . ($lot->date_end ? (' AND date <="' . $lot->date_end . '"') : '') ;
						$count = $db->select($sql) ;
						echo '<b>' . $count->nb . ' (' . round($count->nb / $nbInvitations * 100) . '%)</b>' ;
						?>
						</td>
					</tr>
					<tr>
						<td>Nombre de personnes ayant invitée des amis</td>
						<td>
						<?php 
						$sql = 'SELECT COUNT(DISTINCT(id_user)) nb FROM ' . DB_PREFIX . 'users_invitations_sends' ;
						if($id_lot) $sql .= ' WHERE date >= "' . $lot->date_start . '"' . ($lot->date_end ? (' AND date <="' . $lot->date_end . '"') : '') ;
						$count = $db->select($sql) ;
						echo '<b>' . $count->nb . ' (' . round($count->nb / $nbUsers * 100) . '%)</b>' ;
						?>
						</td>
					</tr>
					<tr>
						<td>Nombre d'amis invités</td>
						<td>
						<?php 
						$sql = 'SELECT COUNT(DISTINCT(id_friend)) nb FROM ' . DB_PREFIX . 'users_invitations_sends' ;
						if($id_lot) $sql .= ' WHERE date >= "' . $lot->date_start . '"' . ($lot->date_end ? (' AND date <="' . $lot->date_end . '"') : '') ;
						$count = $db->select($sql) ;
						echo '<b>' . $count->nb . '</b>' ;
						?>
						</td>
					</tr>
				</table>
				<a href="board.php">Retour</a>
			</div>
		</div>
	</div>
</body>
</html>