<table cellpadding="0" cellspacing="0" background="gfx/tab_game.jpg" width="520" height="670" style="cursor:pointer;">
	<tr>
		<td style="text-align:center;height:285px;">
			<div style="margin-top:18px;">
				<span style="font-size:30px;">Sorry... There’s nothing<br/>to win at the moment.</span>
				<br/><br/>
				<span style="font-size:18px;">Fortunately, other contests are going to happen.<br/>Stay tuned and receive an email when<br/>there’s something new starting.</span>
				<br/><br/>
				<div id="notice">
					<a href="javascript:void(0);" onclick="save(); return false;" class="button" style="margin:0 110px;">Alert me when a new contest starts</a>
				</div>
			</div>
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