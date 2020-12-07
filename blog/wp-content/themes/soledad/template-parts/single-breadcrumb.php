<?php
if( get_theme_mod( 'penci_disable_breadcrumb' ) ){
	return;
}
$yoast_breadcrumb = '';
if ( function_exists( 'yoast_breadcrumb' ) ) {
	$yoast_breadcrumb = yoast_breadcrumb( '<div class="container penci-container-inside penci-breadcrumb single-breadcrumb">', '</div>', false );
}

if( $yoast_breadcrumb ){
	echo $yoast_breadcrumb;
}else{ ?>
<div class="container penci-breadcrumb single-breadcrumb">
	<span><a class="crumb" href="<?php echo esc_url( home_url('/') ); ?>"><?php echo penci_get_setting( 'penci_trans_home' ); ?></a></span><i class="fa fa-angle-right"></i>
	<?php
	if ( get_theme_mod( 'enable_pri_cat_yoast_seo' ) ) {
		$primary_term = penci_get_wpseo_primary_term();

		if ( $primary_term ) {
			echo $primary_term;
		} else {
			$penci_cats = get_the_category( get_the_ID() );
			$penci_cat  = array_shift( $penci_cats );
			echo penci_get_category_parents( $penci_cat );
		}
	} else {
		$penci_cats = get_the_category( get_the_ID() );
		$penci_cat  = array_shift( $penci_cats );
		echo penci_get_category_parents( $penci_cat );
	}
	?>
	<span><?php the_title(); ?></span>
</div>
<?php } ?>