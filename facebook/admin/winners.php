<?php 

include_once '../config.php' ;
include_once 'session.php' ;

include_once '../functions.php' ;

$id_lot = (int) $_GET['id'] ;

$sql = 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE id_lot = ' . $id_lot ; 
$result = mysql_query($sql);
$lot = mysql_fetch_object($result) ;

if(array_key_exists('sent', $_GET))
{
	$id_winner = (int) $_GET['sent'] ;
	
	$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_winner = ' . $id_winner ;
	$result = mysql_query($sql) ;
	$row = mysql_fetch_object($result) ;
	
	if($row && !$row->sent_package)
	{
		$sql = 'UPDATE ' . DB_PREFIX . 'winners SET sent_package = 1 WHERE id_winner = ' . $id_winner ;
		mysql_query($sql) ;
	}
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
				<h3 style="float:none;margin-bottom:1em;padding:3px 0;">Gagnants <?php echo $lot->name_fr ; ?></h3>
				<table>
				<?php 
				$start	= array_key_exists('start', $_GET) ? (int) $_GET['start'] : 0 ;
				$count = 50 ;
				
				$sql  = 'SELECT w.*, u.name, u.fname, u.email, l.name_fr lot FROM ' . DB_PREFIX . 'winners w LEFT JOIN ' . DB_PREFIX . 'users u ON u.id_user = w.id_user LEFT JOIN ' . DB_PREFIX . 'lots l ON l.id_lot = w.id_lot';
				$sql .= ' WHERE w.id_lot = ' . $id_lot ;
				$sql .= ' ORDER BY id_winner DESC LIMIT ' .$start . ',' . $count ;
				$result = mysql_query($sql) ;
				
				while($w = mysql_fetch_array($result))
				{
					echo '<tr>' ;
					
					echo '<td>' . $w['fname'] . ' ' . $w['name'] . '</td>' ;
					echo '<td>' . $w['email'] . '</td>' ;
					echo '<td>1 ' . stripslashes($w['lot']) . '' ;
					echo '<td>le ' . formatDate($w['date'], '%d/%m à %H:%M') . '</td>' ;

					echo '<td width="240">' ;
					echo '<a target="_blank" href="http://www.facebook.com/profile.php?id=' . $w['id_user'] . '">Voir le profil Facebook</a>' ;
					
					if(0 && !$w['sent_package'])
					{
						echo ' - <a href="winners.php?id=' . $id_lot . '&sent=' . $w['id_winner'] . '">Marquer le lot comme envoyé</a>' ;
					}
					
					echo '</td>' ;
					
					echo '</tr>' ;
				}
				
				echo '<tr>' ;
				echo '<td colspan="5" style="padding-top:20px;">' ;
				$sql  = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'winners' ;
				$sql .= ' WHERE id_lot = ' . $id_lot ;
				$result = mysql_query($sql) ;
				$row = mysql_fetch_array($result) ;
				if($row['nb'] > $count)
				{
					for($i = 0 ; $i < ceil($row['nb'] / $count) ; $i++)
					{
						if($i > 0) echo ' - ' ;
						echo '<a class="' . ($i * $count == $start ? 'selected' : '') . '" href="winners.php?id=' . $id_lot . '&start=' . ($i * $count) . '">' . ($i + 1) . '</a>' ; 
					}
				}
				echo '</td>' ;
				echo '</tr>' ;
				?>
				</table>
				<a href="board.php">Retour</a>
			</div>
		</div>
	</div>
</body>
</html>