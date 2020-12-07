<div class="tab end kids">
	<div class="header">
		<img src="<?php echo $config->image_header ; ?>" />
	</div>
	<div class="end_game avis_select">
		<h2 class="big"><?php echo replace_tags($config->text_kids_text1, $lot, $user) ; ?></h2>
		<br/>
		<h2><?php echo replace_tags($config->text_kids_text2, $lot, $user) ; ?></h2>
		
		<div class="kids_list">
		<?php
		$sLang = substr($locale, 0, 2) ;
		$aPastilles = array() ;
		$aPastilles[1] = array('fr' => 'Fleurs', 'en' => 'Flowers') ;
		$aPastilles[2] = array('fr' => 'Fruits & Légumes', 'en' => 'Fruits & Veggies') ;
		$aPastilles[3] = array('fr' => 'Monstres', 'en' => 'Monsters') ;
		$aPastilles[4] = array('fr' => 'Fées', 'en' => 'Fairies') ;
		$aPastilles[5] = array('fr' => 'Animaux sauvages', 'en' => 'Wild animals') ;
		$aPastilles[6] = array('fr' => 'Pirates', 'en' => 'Pirates') ;
		$aPastilles[7] = array('fr' => 'Princesses', 'en' => 'Princesses') ;
		$aPastilles[8] = array('fr' => 'Voitures', 'en' => 'Cars') ;
		$aPastilles[9] = array('fr' => 'Chevaliers', 'en' => 'Knights') ;
		for($i = 1 ; $i <= 9 ; $i++)
		{
			echo '<a href="#" class="avis' . $i . '" data-avis="' . $i . '">' ;
			echo '<img src="images/kids/pastille' . $i . '.png" class="normal" />' ;
			echo '<img src="images/kids/pastille' . $i . '_over.png" class="over"/>' ;
			echo $aPastilles[$i][$sLang] ;
			echo '</a>' ;
		}
		?>
		</div>
	</div>
	<div class="end_game avis_done" style="display:none;">
		<h2 class="big"><?php echo replace_tags($config->text_kids_text3, $lot, $user) ; ?> ♥</h2>
		<br/>
		<h2><?php echo replace_tags($config->text_kids_text4, $lot, $user) ; ?></h2><br/><br/>
		<a href="http://www.monbento.com/kids/index<?php echo substr($locale, 0, 2) == 'fr' ? '' : '.en' ; ?>.php?utm_campaign=concourskids&utm_source=facebook-application&utm_medium=socialmedia" target="_blank" class="button"><?php echo $config->text_kids_btnproducts ; ?></a>
		<div class="kids_list">
		<?php 
		$avis = $db->select('SELECT * FROM ' . DB_PREFIX . 'users_kidsavis WHERE id_user = "' . mysql_escape_string($user->id_user) . '"') ;
		
		if($avis)
		{
			echo '<a href="javascript:void(0)" class="avis1" style="cursor:default;">' ;
			echo '<img src="images/kids/pastille' . $avis->avis1 . '.png" class="normal" />' ;
			echo '<img src="images/kids/pastille' . $avis->avis1 . '_over.png" class="over"/>' ;
			echo $aPastilles[$avis->avis1][$sLang] ;
			echo '</a>' ;
			
			echo '<a href="javascript:void(0)" class="avis2" style="cursor:default;">' ;
			echo '<img src="images/kids/pastille' . $avis->avis2 . '.png" class="normal" />' ;
			echo '<img src="images/kids/pastille' . $avis->avis2 . '_over.png" class="over"/>' ;
			echo $aPastilles[$avis->avis2][$sLang] ;
			echo '</a>' ;
			
			echo '<a href="javascript:void(0)" class="avis3" style="cursor:default;">' ;
			echo '<img src="images/kids/pastille' . $avis->avis3 . '.png" class="normal" />' ;
			echo '<img src="images/kids/pastille' . $avis->avis3 . '_over.png" class="over"/>' ;
			echo $aPastilles[$avis->avis3][$sLang] ;
			echo '</a>' ;
		}
		?>
		</div>
	</div>
</div>
<?php 
	include_once 'footer.php' ;
?>
<script>
$('.kids_list > a ').click(onPastilleClick) ;

var aAvis = [] ;

function onPastilleClick()
{
	if($(this).hasClass('selected'))
	{
		$(this).removeClass('selected') ;

		aAvis.splice(aAvis.indexOf($(this).attr('data-avis')), 1);
	}else{
		$(this).addClass('selected') ;

		aAvis.push($(this).attr('data-avis')) ;
	}

	if($('.kids_list > a.selected').length == 3)
	{
		$('.avis_select').css('display', 'none') ;
		$('.avis_done').css('display', 'block') ;

		$.post('pages/add_avis.php', {signed_request:signed_request,id_user:id_user,avis1:aAvis[0],avis2:aAvis[1],avis3:aAvis[2]});

		var html = '' ;
		html += '<a href="javascript:void(0)" class="avis1" style="cursor:default;"><img src="images/kids/pastille' + aAvis[0] + '.png" class="normal" /><img src="images/kids/pastille' + aAvis[0] + '.png" class="over" /></a>' ;
		html += '<a href="javascript:void(0)" class="avis2" style="cursor:default;"><img src="images/kids/pastille' + aAvis[1] + '.png" class="normal" /><img src="images/kids/pastille' + aAvis[1] + '.png" class="over" /></a>' ;
		html += '<a href="javascript:void(0)" class="avis3" style="cursor:default;"><img src="images/kids/pastille' + aAvis[2] + '.png" class="normal" /><img src="images/kids/pastille' + aAvis[2] + '.png" class="over" /></a>' ;

		$('.avis_done .kids_list').html(html) ;
			
		$('.kids').height(600) ;
		
		//FB.Canvas.scrollTo(0, 0);
	}

	return false ;
}
<?php if($avis) { ?>
$('.avis_select').css('display', 'none') ;
$('.avis_done').css('display', 'block') ;
<?php } ?>
</script>
