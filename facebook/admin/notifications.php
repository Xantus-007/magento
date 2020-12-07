<?php 

include_once '../config.php' ;
include_once 'session.php' ;

include_once '../functions.php' ;
	
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
				<h3 style="float:none;margin-bottom:1em;padding:3px 0;">Listes des notifications</h3>
				<table>
				<?php 
				$lines = $db->selectAll('SELECT * FROM ' . DB_PREFIX . 'notifications ORDER BY id_lot, id_group, id_notif') ;
				
				$i = 0 ;
				$id_lot = 0 ;
				$id_group = 0 ;
				foreach($lines as $line)
				{
					if($line->id_lot != $id_lot)
					{
						echo '<tr class="">' ;
						
						$lot = $db->select('SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $line->id_lot) ;
						
						echo '<td colspan="6"><b style="text-decoration:underline;">' . $lot->name_fr . '</b></td>'  ;
					
						echo '</tr>' ;
					
						$id_lot = $line->id_lot ;
					}
					
					if($line->id_group != $id_group)
					{
						echo '<tr class="">' ;
						
						echo '<td colspan="6"><b>Groupe ' . $line->id_group . '</b></td>'  ;
						
						echo '</tr>' ;
						
						$id_group = $line->id_group ;
					}
					
					echo '<tr class="">' ;
					
					echo '<td width="150">' . stripslashes($line->name) . '</td>' ;
					if($line->nb)
					{
						echo '<td>' . $line->nb . ' personnes</td>' ;
					}else{
						echo '<td>Tous</td>' ;
					}
					echo '<td  width="300">' . $line->url . '</td>' ;
					echo '<td>' . $line->ref . '</td>' ;
					
					$count = $db->select('SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'users_notifications_sends WHERE sent = 1 AND id_notif = ' . $line->id_notif) ;
					$count1 = $db->select('SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'users_notifications_sends WHERE id_notif = ' . $line->id_notif) ;
					echo '<td>' . $count->nb . '/' . $count1->nb . ' envoyées</td>' ;
					
					echo '<td><a href="edit_notif.php?id=' . $line->id_notif . '">Modifier</a></td>' ;
					
					echo '</tr>' ;
					
					$i++ ;
				}
				?>
				</table>
				<div>
					<a href="edit_notif.php">Créer une notification</a>
				</div>
			</div>
		</div>
	</div>
</body>
</html>