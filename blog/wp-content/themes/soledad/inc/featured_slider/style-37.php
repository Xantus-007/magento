<?php
/**
 * Template part for Slider Style 37
 */

$feat_query = penci_get_query_featured_slider();
if ( ! $feat_query ) {
	return;
}
$slider_title_length = get_theme_mod( 'penci_slider_title_length' ) ? get_theme_mod( 'penci_slider_title_length' ) : 12;
?>
<?php if ( $feat_query->have_posts() ) : ?>
<?php $i = 1; $num_posts = $feat_query->post_count; $number_last = $num_posts - 1;
while ( $feat_query->have_posts() ) : $feat_query->the_post();
$thumb = 'penci-magazine-slider';
if( $i == $number_last || $i == $num_posts ): $thumb = 'penci-thumb'; endif;
?>
	<div class="item">
		<div class="penci-item-mag penci-item-<?php if ( $i != $number_last && $i != $num_posts ){ echo '1'; } else { echo '2'; } ?>">
			<?php if( ! get_theme_mod( 'penci_disable_lazyload_slider' ) ) { ?>
				<a class="penci-image-holder <?php if ( $i != $number_last && $i != $num_posts ){ echo 'owl-lazy'; } else { echo 'penci-lazy'; } ?>" data-src="<?php echo penci_get_featured_image_size( get_the_ID(), $thumb ); ?>" href="<?php the_permalink(); ?>" title="<?php echo wp_strip_all_tags( get_the_title() ); ?>"></a>
			<?php } else { ?>
				<a class="penci-image-holder" style="background-image: url('<?php echo penci_get_featured_image_size( get_the_ID(), $thumb ); ?>');" href="<?php the_permalink(); ?>" title="<?php echo wp_strip_all_tags( get_the_title() ); ?>"></a>
			<?php }?>
			<div class="penci-slide-overlay penci-slider6-overlay penci-slider37-overlay">
				<a class="overlay-link" href="<?php the_permalink(); ?>"></a>
				<?php if( ! get_theme_mod( 'penci_featured_slider_icons' ) && ( has_post_format( 'video' ) || has_post_format( 'audio' ) || has_post_format( 'link' ) || has_post_format( 'quote' ) || has_post_format( 'gallery' ) ) ): ?>
					<a href="<?php the_permalink(); ?>" class="overlay-icon-format <?php if( $i%3 == 1 ): echo ' lager-size-icon'; endif; ?>">
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
					</a>
				<?php endif; ?>
				<div class="penci-mag-featured-content">
					<div class="feat-text<?php if ( get_theme_mod( 'penci_featured_meta_date' ) ): echo ' slider-hide-date'; endif;?>">
						<?php if ( $i != $number_last && $i != $num_posts ): ?>
							<?php if ( ! get_theme_mod( 'penci_featured_hide_categories' ) ): ?>
								<div class="cat featured-cat"><?php penci_category( '' ); ?></div>
							<?php endif; ?>
						<?php endif; ?>
						<h3><a title="<?php echo wp_strip_all_tags( get_the_title() ); ?>" href="<?php the_permalink() ?>"><?php echo wp_trim_words( wp_strip_all_tags( get_the_title() ), $slider_title_length, '...' ); ?></a></h3>
						<?php if ( ! get_theme_mod( 'penci_featured_meta_comment' ) || ! get_theme_mod( 'penci_featured_meta_date' ) ): ?>
							<div class="feat-meta">
								<?php if ( ! get_theme_mod( 'penci_featured_meta_date' ) ): ?>
									<span class="feat-time"><?php penci_soledad_time_link(); ?></span>
								<?php endif; ?>
								<?php if ( ! get_theme_mod( 'penci_featured_meta_comment' ) ): ?>
									<span class="feat-comments"><a href="<?php comments_link(); ?> "><?php comments_number( '0 ' . penci_get_setting( 'penci_trans_comment' ), '1 '. penci_get_setting( 'penci_trans_comment' ), '% ' . penci_get_setting( 'penci_trans_comments' ) ); ?></a></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
			
	</div>
	
	<?php if( $i == ( $num_posts - 2 ) ):  echo '</div></div><div class="penci-featured-items-right">';  endif; ?>
	
	<?php $i++; endwhile; wp_reset_postdata(); ?>
	
<?php endif; ?>