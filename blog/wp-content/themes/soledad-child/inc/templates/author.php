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
        <?php if(!is_author()) { ?>
			<a href="<?php echo get_home_url() ?>/author/<?php the_author_meta( 'user_nicename',$ID ); ?>" >
			<?php
		}
            if( get_field( 'image_user', 'user_'.$ID ) ){
                echo '<img src="'.get_field( 'image_user', 'user_'.$ID ).'" width="155" />';
            }else {
                echo get_avatar( get_the_author_meta( $ID ), '155' );
			}
		 if(is_author()) { 
            ?>
		</a>
		 <?php } ?>
	</div>
	<div class="author-content">
        <h2><?php echo get_the_author_meta( 'first_name',$ID ).' '.get_the_author_meta( 'last_name',$ID ); ?></h2>
        <div class="Socialicons">
            <?php if ( get_the_author_meta( 'user_url' ,$ID) ) : ?>
			<a target="_blank" class="author-social" href="<?php the_author_meta( 'user_url'); ?>"><i class="fa fa-globe"></i></a>
            <?php endif; ?>
            <?php if ( get_the_author_meta( 'facebook' ,$ID) ) : ?>
                <a target="_blank" class="author-social" href="<?php echo esc_attr( the_author_meta( 'facebook' ,$ID) ); ?>"><i class="fa fa-facebook"></i></a>
            <?php endif; ?>
            <?php if ( get_the_author_meta( 'twitter' ,$ID) ) : ?>
                <a target="_blank" class="author-social" href="<?php echo esc_attr( the_author_meta( 'twitter' ,$ID) ); ?>"><i class="fa fa-twitter"></i></a>
            <?php endif; ?>
            <?php if ( get_the_author_meta( 'youtube' ,$ID) ) : ?>
                <a target="_blank" class="author-social" href="<?php echo esc_attr( the_author_meta( 'youtube' ,$ID) ); ?>"><i class="fa fa-youtube"></i></a>
            <?php endif; ?>
            <?php if ( get_the_author_meta( 'linkedin' ,$ID) ) : ?>
                <a target="_blank" class="author-social" href="<?php echo esc_attr( the_author_meta( 'linkedin' ,$ID) ); ?>"><i class="fa fa-linkedin"></i></a>
            <?php endif; ?>
            <?php if ( get_the_author_meta( 'soundcloud' ,$ID) ) : ?>
                <a target="_blank" class="author-social" href="<?php echo esc_attr( the_author_meta( 'soundcloud' ,$ID) ); ?>"><i class="fa fa-soundcloud"></i></a>
            <?php endif; ?>
            <?php if ( get_the_author_meta( 'google' ,$ID) ) : ?>
                <a target="_blank" class="author-social" href="<?php echo esc_attr( the_author_meta( 'google' ,$ID) ); ?>?rel=author"><i class="fa fa-google-plus"></i></a>
            <?php endif; ?>
            <?php if ( get_the_author_meta( 'instagram' ,$ID) ) : ?>
                <a target="_blank" class="author-social" href="<?php echo esc_attr( the_author_meta( 'instagram' ,$ID) ); ?>"><i class="fa fa-instagram"></i></a>
            <?php endif; ?>
            <?php if ( get_the_author_meta( 'pinterest' ,$ID) ) : ?>
                <a target="_blank" class="author-social" href="<?php echo esc_attr( the_author_meta( 'pinterest' ,$ID) ); ?>"><i class="fa fa-pinterest"></i></a>
            <?php endif; ?>
            <?php if ( get_the_author_meta( 'tumblr' ,$ID) ) : ?>
                <a target="_blank" class="author-social" href="<?php echo esc_attr( the_author_meta( 'tumblr' ,$ID) ); ?>"><i class="fa fa-tumblr"></i></a>
            <?php endif; ?>
            <?php if ( get_the_author_meta( 'email' ,$ID) ) : ?>
                <a class="author-social" href="mailto:<?php echo esc_attr( the_author_meta( 'email' ,$ID) ); ?>"><i class="fa fa-envelope-o"></i></a>
            <?php endif; ?>
        </div>
        <p><?php the_author_meta( 'description',$ID ); ?></p>
        <a class="Authrecette" href="<?php echo get_home_url() ?>/author/<?php the_author_meta( 'user_nicename',$ID ); ?>" ><i class="far fa-chevron-right"></i><?php echo __('ses recettes','soledad'); ?></a>
	</div>
</div>