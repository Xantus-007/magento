<?php 

include_once '../config.php' ;
include_once 'session.php' ;

include_once '../functions.php' ;

if(array_key_exists('load', $_GET))
{
	$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ;
	$result = mysql_query($sql) ;
	$row = mysql_fetch_object($result) ;
	
	if(!$row)
	{
		$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 0 AND date_start IS NULL ORDER BY id_lot' ;
		$result = mysql_query($sql) ;
		$row = mysql_fetch_object($result) ;
		
		if($row && $row->id_lot)
		{
			$sql = 'UPDATE ' . DB_PREFIX . 'lots SET state = 1, date_start = NOW() WHERE id_lot = ' . $row->id_lot ;
			mysql_query($sql) ;
		}
	}
}

if(array_key_exists('unload', $_GET))
{
	$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ;
	$result = mysql_query($sql) ;
	$row = mysql_fetch_object($result) ;
	
	if($row && $row->id_lot)
	{
		$sql = 'UPDATE ' . DB_PREFIX . 'lots SET state = 0, date_end = NOW() WHERE id_lot = ' . $row->id_lot ;
		mysql_query($sql) ;
	}
}

$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 1' ;
$result = mysql_query($sql) ;
$current = mysql_fetch_array($result) ;			

$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 0 AND date_end IS NULL' ;
$result = mysql_query($sql) ;
$waiting = mysql_fetch_array($result) ;	
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
				<h3 style="float:none;margin-bottom:1em;padding:3px 0;">Concours <?php echo $current ? 'en cours' : 'en attente' ;?></h3>
				<div style="position:absolute;right:10px;top:20px;">
					<?php 
					if($current)
					{
						echo '<a href="board.php?unload=1">Terminer le concours en cours</a>' ;
					}
					
					if($waiting)
					{
						echo '<a href="board.php?load=1">Activer le concours en attente</a>' ;
					}
					
					if(!$current && !$waiting)
					{
						echo '<a href="edit.php">Ajouter un concours</a>' ;
					}
					?>
				</div>
				<table>
				<?php 
				$start	= array_key_exists('start', $_GET) ? (int) $_GET['start'] : 0 ;
				$count = 20 ;
				
				$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state >= 0 AND date_end IS NULL ORDER BY state DESC, id_lot ASC LIMIT ' .$start . ',' . $count ;
				$result = mysql_query($sql) ;
				
				$i = 0 ;
				while($l = mysql_fetch_array($result))
				{
					echo '<tr class="' . ($l['state'] ? 'selected' : '') . '">' ;
					
					echo '<td width="180">' . stripslashes($l['name']) . '</td>' ;
					echo '<td>' . $l['nb'] . ' lots</td>' ;
					echo '<td>Gain tous les ' . $l['frequency'] . '</td>' ;
					echo '<td>' . $l['win'] . ' gagnés' ;
					
					$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'winners WHERE id_lot = ' . $l['id_lot'] . ' ORDER BY id_winner DESC LIMIT 0,4' ;
					$result1 = mysql_query($sql) ;
					$row = mysql_fetch_array($result1) ;
					
					$sql = 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'participations WHERE id_lot = ' . $l['id_lot'] ;
					if($row)
					{
						echo ' (dernier le ' . formatDate($row['date'], '%d %B à %H:%M') . ')' ;
						$sql.= ' AND date > "' . $row['date'] . '"' ;
					}
					echo '</td>' ;
					
					$result = mysql_query($sql);
					$row = mysql_fetch_array($result) ;
					echo '<td>' . $row['nb'] . ' participations</td>' ;
					
					echo '<td>' ;
					echo '<a href="edit.php?id=' . $l['id_lot'] . '">Modifier</a>' ;
					if($l['win'])
					{
						echo ' - <a href="winners.php?id=' . $l['id_lot'] . '">Voir les gagnants</a>' ;
					}
					echo '</td>' ;
					
					echo '</tr>' ;
					
					$i++ ;
				}
				
				if($i == 0) echo '<tr><td class="center" colspan="5">Aucun concours en ' . ($current ? 'cours' : 'attente') . '</td></tr>' ;
				
				echo '<tr>' ;
				echo '<td colspan="5" style="padding-top:20px;">' ;
				$sql 	= 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'lots WHERE state >= 0 AND date_end IS NULL' ;
				$result = mysql_query($sql) ;
				$row = mysql_fetch_array($result) ;
				if($row['nb'] > $count)
				{
					for($i = 0 ; $i < ceil($row['nb'] / $count) ; $i++)
					{
						if($i > 0) echo ' - ' ;
						echo '<a class="' . ($i * $count == $start ? 'selected' : '') . '" href="board.php?start=' . ($i * $count) . '">' . ($i + 1) . '</a>' ; 
					}
				}
				echo '</td>' ;
				echo '</tr>' ;
				?>
				</table>
				
				<?php 
				$sql 	= 'SELECT COUNT(*) nb FROM ' . DB_PREFIX . 'lots WHERE state = 0 AND date_end IS NOT NULL' ;
				$result = mysql_query($sql) ;
				$row = mysql_fetch_array($result) ;
				
				if($row['nb'])
				{
					$sql 	= 'SELECT * FROM ' . DB_PREFIX . 'lots WHERE state = 0 AND date_end IS NOT NULL ORDER BY id_lot DESC LIMIT 0, 10' ;
					$result = mysql_query($sql) ;
					
					echo '<h3 style="float:none;margin-bottom:1em;padding:3px 0;">Concours terminés</h3>' ;
					
					echo '<table>' ;
					
					while($l = mysql_fetch_array($result))
					{
						echo '<tr class="' . ($l['state'] ? 'selected' : '') . '">' ;
						
						echo '<td width="180">' . stripslashes($l['name']) . '</td>' ;
						echo '<td>' . $l['nb'] . ' lots</td>' ;
						echo '<td>Gain tous les ' . $l['frequency'] . '</td>' ;
						echo '<td>' . $l['win'] . ' gagnés</td>' ;
						
						echo '<td>' ;
						echo 'du ' . formatDate($l['date_start'], '%d %B') . '' ;
						echo ' au ' . formatDate($l['date_end'], '%d %B') . '' ;
						echo '</td>' ;
						
						echo '<td>' ;
						echo '<a href="winners.php?id=' . $l['id_lot'] . '">Voir les gagnants</a>' ;
						echo '</td>' ;
						
						echo '</tr>' ;
					}
					
					echo '</table>' ;
				}
				?>
				<br/>
				<div>
					<a href="config.php">Configuration</a> - <a href="export.php">Exporter les participants</a>
				</div>
			</div>
		</div>
	</div>
</body>
</html>