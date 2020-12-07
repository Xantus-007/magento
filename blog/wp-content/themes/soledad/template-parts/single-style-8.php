<?php
$sidebar_enable = penci_single_sidebar_return();
$sidebar_position = penci_get_posts_sidebar_class();
$sidebar_small_width = penci_single_smaller_content_enable();

// Check class main content
$class_container_single = 'container container-single penci-single-style-8 penci-single-smore';
if ( $sidebar_enable ){
	$class_container_single .= ' penci_sidebar';
	$class_container_single .= ' ' . $sidebar_position;
} else {
	$class_container_single .= ' penci_is_nosidebar';
}

if( $sidebar_small_width ) {
	$class_container_single .= ' penci-single-smaller-width';
}

if( ! get_theme_mod( 'penci_disable_lightbox_single' ) ){
	$class_container_single .= ' penci-enable-lightbox';
}

$post_format = get_post_format();
$show_post_format = true;
if( get_theme_mod( 'penci_post_thumb' ) && ! in_array( $post_format, array( 'link', 'quote','gallery','video' ) )  ) {
	$class_container_single .= ' penci-single-pheader-noimg';
	$show_post_format = false;
}
get_template_part( 'template-parts/single', 'breadcrumb' );
?>
<div class="<?php echo $class_container_single; ?>">
	<div id="main"<?php if ( get_theme_mod( 'penci_sidebar_sticky' ) ): ?> class="penci-main-sticky-sidebar"<?php endif; ?>>
		<div class="theiaStickySidebar">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php penci_set_post_views( $post->ID ); ?>
				<?php get_template_part( 'template-parts/single', 'content-main' ); ?>
			<?php endwhile; endif; ?>
		</div>
	</div>
<?php
if ( $sidebar_enable ) {
	get_sidebar();
}
get_footer();