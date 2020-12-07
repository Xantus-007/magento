<?php
/**
 * Related post template
 * Render list related posts
 *
 * @since 1.0
 */
$data_auto = 'true';
$auto = get_theme_mod( 'penci_post_related_autoplay' );
if( $auto == false ): $data_auto = 'false'; endif;

$sidebar_opts = get_post_meta( get_the_ID(), 'penci_post_sidebar_display', true );

$orig_post = $post;
global $post;
$numbers_related = get_theme_mod('penci_numbers_related_post');
if ( !isset( $numbers_related ) || $numbers_related < 1 ): $numbers_related = 10; endif;

$orderby_post = 'date';
if( get_theme_mod('penci_related_orderby') && get_theme_mod('penci_related_orderby') != 'date' ):
	$orderby_post = get_theme_mod('penci_related_orderby');
endif;

$related_order_post  = get_theme_mod('penci_related_sort_order');
$related_order_post   = $related_order_post ? $related_order_post : 'DESC';
$related_title_length = get_theme_mod( 'penci_related_posts_title_length' ) ? get_theme_mod( 'penci_related_posts_title_length' ) : 8;
$penci_related_by = get_theme_mod('penci_related_by');

$args = penci_get_query_related_posts( get_the_ID(), $penci_related_by, $orderby_post, $related_order_post, $numbers_related );

if ( ! empty( $args ) ) {

	$my_query = new wp_query( $args );
	if ( $my_query->have_posts() ) {
		$data_loop = '';
		$number_posts_display = $my_query->post_count;
		if( $number_posts_display < 4 ):
		$data_loop = ' data-loop="false"';
		endif;
		?>
		<div class="post-related<?php if( get_theme_mod('penci_post_related_grid') ): echo ' penci-posts-related-grid'; endif; ?>">
		<div class="post-title-box"><h4 class="post-box-title"><?php echo __(penci_get_setting( 'penci_post_related_text'),'soledad' ); ?></h4></div>
		<?php if( ! get_theme_mod('penci_post_related_grid') ) { $lazy_class = 'owl-lazy'; ?>
		<div class="penci-owl-carousel penci-owl-carousel-slider penci-related-carousel" data-lazy="true"<?php echo $data_loop; ?> data-item="3" data-desktop="3" data-tablet="2" data-tabsmall="2" data-auto="<?php echo $data_auto; ?>" data-speed="300"<?php if( ! get_theme_mod('penci_post_related_dots') ){ echo ' data-dots="true"'; } if( ! get_theme_mod('penci_post_related_arrows') ){ echo ' data-nav="false"'; } ?>>
		<?php } else { $lazy_class = 'penci-lazy'; ?>
		<div class="penci-related-carousel penci-related-grid-display">
		<?php } ?>
		<?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
				<div class="item-related">
					<?php if ( ( function_exists( 'has_post_thumbnail' ) ) && ( has_post_thumbnail() ) ) : ?>
						<?php if( ! get_theme_mod( 'penci_disable_lazyload_single' ) ) { ?>
							<a class="related-thumb penci-image-holder <?php echo $lazy_class; ?>" data-src="<?php echo penci_get_featured_image_size( get_the_ID(), penci_featured_images_size() ); ?>" href="<?php the_permalink(); ?>" title="<?php echo wp_strip_all_tags( get_the_title() ); ?>">
						<?php } else { ?>
							<a class="related-thumb penci-image-holder" style="background-image: url('<?php echo penci_get_featured_image_size( get_the_ID(), penci_featured_images_size() ); ?>');" href="<?php the_permalink(); ?>" title="<?php echo wp_strip_all_tags( get_the_title() ); ?>">
						<?php }?>
							<?php if( has_post_thumbnail() && get_theme_mod('penci_post_related_icons') ): ?>
								<?php if ( has_post_format( 'video' ) ) : ?>
									<i class="fa fa-play"></i>
								<?php endif; ?>
								<?php if ( has_post_format( 'audio' ) ) : ?>
									<i class="fa fa-music"></i>
								<?php endif; ?>
								<?php if ( has_post_format( 'link' ) ) : ?>
									<i class="fa fa-link"></i>
								<?php endif; ?>
								<?php if ( has_post_format( 'quote' ) ) : ?>
									<i class="fa fa-quote-left"></i>
								<?php endif; ?>
								<?php if ( has_post_format( 'gallery' ) ) : ?>
									<i class="fa fa-picture-o"></i>
								<?php endif; ?>
							<?php endif; ?>
						</a>
					<?php endif; ?>
					<h3><a href="<?php the_permalink(); ?>"><?php echo wp_trim_words( wp_strip_all_tags( get_the_title() ), $related_title_length, '...' ); ?></a></h3>
					<?php if( ! get_theme_mod('penci_hide_date_related') ): ?>
					<span class="date"><?php penci_soledad_time_link(); ?></span>
					<?php endif; ?>
				</div>
		<?php
		endwhile;
		echo '</div></div>';
	}
}
$post = $orig_post;
wp_reset_postdata();
?>