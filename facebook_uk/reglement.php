<?php 
include_once 'config.php' ;

$config = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration') ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"> 
<head>
	<meta http-equiv="Content-language" content="fr" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Réglement</title>
	<link rel="stylesheet" href="css/global.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
</head>
<body>
	<div id="content">
		<table style="width:660px;margin:20px 20px 0;">
			<tr>
				<td style="font-size:20px;">
					Contest regulations
				</td>
			</tr>
		</table>
		<table style="width:660px;margin:20px;">
			<tr>
				<td style="font-size:10px;">
					<?php 
						echo str_replace(chr(10), '<br/>', $config->regulations) ;
					?>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>