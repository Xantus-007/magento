<?php
/**
 * Display detail author of current post
 * Use in single post
 *
 * @since 1.0
 */
?>
<div class="post-author">
	<div class="author-img">
		<?php if( function_exists('get_simple_local_avatar') ){
			if( get_simple_local_avatar( get_the_author_meta( 'ID' ) ) ){
				echo get_simple_local_avatar( get_the_author_meta( 'ID' ), '100' );
			} else {
				echo get_avatar( get_the_author_meta( 'email' ), '100' );
			}
		} else {
			echo get_avatar( get_the_author_meta( 'email' ), '100' );
		}	
		?>
	</div>
	<div class="author-content">
		<h5><a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>" ><?php echo get_the_author_meta( 'last_name' ).' '.get_the_author_meta( 'first_name' ); ?></a></h5>
		<p><?php the_author_meta( 'description' ); ?></p>
		<?php if ( get_the_author_meta( 'user_url' ) ) : ?>
			<a target="_blank" class="author-social" href="<?php the_author_meta( 'user_url'); ?>"><i class="fa fa-globe"></i></a>
		<?php endif; ?>
		<?php if ( get_the_author_meta( 'facebook' ) ) : ?>
			<a target="_blank" class="author-social" href="http://facebook.com/<?php echo esc_attr( the_author_meta( 'facebook' ) ); ?>"><i class="fa fa-facebook"></i></a>
		<?php endif; ?>
		<?php if ( get_the_author_meta( 'twitter' ) ) : ?>
			<a target="_blank" class="author-social" href="http://twitter.com/<?php echo esc_attr( the_author_meta( 'twitter' ) ); ?>"><i class="fa fa-twitter"></i></a>
		<?php endif; ?>
		<?php if ( get_the_author_meta( 'google' ) ) : ?>
			<a target="_blank" class="author-social" href="http://plus.google.com/<?php echo esc_attr( the_author_meta( 'google' ) ); ?>?rel=author"><i class="fa fa-google-plus"></i></a>
		<?php endif; ?>
		<?php if ( get_the_author_meta( 'instagram' ) ) : ?>
			<a target="_blank" class="author-social" href="http://instagram.com/<?php echo esc_attr( the_author_meta( 'instagram' ) ); ?>"><i class="fa fa-instagram"></i></a>
		<?php endif; ?>
		<?php if ( get_the_author_meta( 'pinterest' ) ) : ?>
			<a target="_blank" class="author-social" href="http://pinterest.com/<?php echo esc_attr( the_author_meta( 'pinterest' ) ); ?>"><i class="fa fa-pinterest"></i></a>
		<?php endif; ?>
		<?php if ( get_the_author_meta( 'tumblr' ) ) : ?>
			<a target="_blank" class="author-social" href="http://<?php echo esc_attr( the_author_meta( 'tumblr' ) ); ?>.tumblr.com/"><i class="fa fa-tumblr"></i></a>
		<?php endif; ?>
		<?php if ( get_the_author_meta( 'email' ) && get_theme_mod( 'penci_post_author_email' ) ) : ?>
			<a class="author-social" href="mailto:<?php echo esc_attr( the_author_meta( 'email' ) ); ?>"><i class="fa fa-envelope-o"></i></a>
		<?php endif; ?>
	</div>
</div>