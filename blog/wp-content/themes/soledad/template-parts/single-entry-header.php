<div class="header-standard header-classic single-header">
	<?php if ( ! get_theme_mod( 'penci_post_cat' ) ) : ?>
		<div class="penci-standard-cat penci-single-cat"><span class="cat"><?php penci_category( '' ); ?></span></div>
	<?php endif; ?>

	<h1 class="post-title single-post-title entry-title"><?php the_title(); ?></h1>
	<?php penci_soledad_meta_schema(); ?>
	<?php if ( ! get_theme_mod( 'penci_single_meta_author' ) || ! get_theme_mod( 'penci_single_meta_date' ) || ! get_theme_mod( 'penci_single_meta_comment' ) || get_theme_mod( 'penci_single_show_cview' ) ) : ?>
		<div class="post-box-meta-single">
			<?php if ( ! get_theme_mod( 'penci_single_meta_author' ) ) : ?>
				<span class="author-post byline"><span class="author vcard"><?php echo penci_get_setting( 'penci_trans_by' ); ?> <a class="author-url url fn n" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a></span></span>
			<?php endif; ?>
			<?php if ( ! get_theme_mod( 'penci_single_meta_date' ) ) : ?>
				<span><?php penci_soledad_time_link(); ?></span>
			<?php endif; ?>
			<?php if ( ! get_theme_mod( 'penci_single_meta_comment' ) ) : ?>
				<span><?php comments_number( '0 ' . penci_get_setting( 'penci_trans_comment' ), '1 ' . penci_get_setting( 'penci_trans_comment' ), '% ' . penci_get_setting( 'penci_trans_comments' ) ); ?></span>
			<?php endif; ?>
			<?php if ( get_theme_mod( 'penci_single_show_cview' ) ) : ?>
				<span><i class="penci-post-countview-number"><?php echo penci_get_post_views( get_the_ID() ); ?></i> <?php echo penci_get_setting( 'penci_trans_text_views' ); ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
