<?php 
include_once '../config.php' ;
session_start() ;
if(array_key_exists('loggued', $_SESSION) && $_SESSION['loggued'] == 1)
{
	header('Location:board.php') ;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../css/screen.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="../css/admin.css?v=<?php echo VERSION ; ?>" type="text/css" media="screen, projection" />
</head>
<body>
	<div class="container" id="main">
		<div class="span-24 last" id="content">
			<div class="content">
				<h3 style="float:none;margin-bottom:1em;padding:3px 0;">Connexion</h3>
				<form method="post" action="board.php" id="login">
				<table>
					<tbody><tr>
						<td>
							<label>Votre login</label>
						</td>
						<td>
							<input type="text" value="" id="adm_login" name="adm_login" />
						</td>
					</tr>
					<tr>
						<td>
							<label>Votre mot de passe</label>
						</td>
						<td>
							<input type="password" value="" id="adm_pswd" name="adm_pswd" />
						</td>
					</tr>
					<tr>
						<td>
							
						</td>
						<td>
							<input type="submit" class="submit" value="Se connecter" id="send" name="send">			</td>
					</tr>
					<tr>
						<td colspan="2">
							<div id="notice"></div>
						</td>
					</tr>
				</tbody></table>
				</form>
			</div>
		</div>
	</div>
</body>
</html>