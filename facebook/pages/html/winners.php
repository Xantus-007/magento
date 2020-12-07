<?php 
if(count($winners))
{					
?>
<div class="winners">
	<h2><?php echo $config->text_winners ; ?></h2>
	<table>
		<tr>
			<?php 
				foreach($winners as $u)
				{
					echo '<td width="56">' ;
					echo '<img width="50" height="50" src="https://graph.facebook.com/' . $u->id_user . '/picture" />' ;
					echo '</td>' ;
				}
			?>
		</tr>
	</table>
</div>
<?php } ?>