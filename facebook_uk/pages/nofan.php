<table cellpadding="0" cellspacing="0" background="gfx/tab_game.jpg" width="520" height="670" style="cursor:pointer;">
	<tr>
		<td style="text-align:center;height:265px;vertical-align:middle;">
			<span style="font-size:36px;line-height:150%;">Become a fan<br/>to enter the contest :</span>
			<br/><br/><br/>
			<span style="font-size:22px;line-height:100%;">Try your luck to win prizes</span>
		</td>
	</tr>
	<tr>
		<td style="padding:0 200px;height:20px;">
			<fb:like layout="button_count" href="<?php echo FB_URL_PAGE ; ?>" show_faces="false" width="100"></fb:like>
		</td>
	</tr>
	<tr>
		<td style="height:353px;text-align:center;vertical-align:bottom;padding-bottom:2px;">
			<?php 
			$config = $db->select('SELECT * FROM ' . DB_PREFIX . 'configuration') ;
			?>
			<img src="lots/<?php echo $config->front ; ?>" height="350"/>
		</td>
	</tr>
	<?php 
		include_once 'footer.php' ;
	?>
</table>
<?php 
	include_once 'disclaimer.php' ;
?>