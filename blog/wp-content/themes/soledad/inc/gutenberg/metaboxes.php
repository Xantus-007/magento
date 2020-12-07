<?php
if ( ! class_exists( 'Penci_Gutenberg_Post_Format' ) ):
	class Penci_Gutenberg_Post_Format {
		public function __construct() {
			if ( is_admin()  ) {
				add_action( 'load-post.php', array( $this, 'init_metabox' ) );
				add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
			}
		}

		public function init_metabox() {
			add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		}

		public function add_metabox( $post_type ) {
			add_meta_box( 'penci-gutenberg-format', esc_html__( 'Post Format Data', 'soledad' ), array( $this, 'render_metabox' ), array( 'post' ), 'side', 'high' );

			if ( post_type_supports( $post_type, 'post-formats' ) && current_theme_supports( 'post-formats' ) ) {
				wp_enqueue_script( 'penci-gutenberg-formats-ui', get_template_directory_uri() . '/inc/gutenberg/admin.js', array( 'vp-post-formats-ui' ), '1.0' );
			}
		}

		public function render_metabox( $post ) {
			vp_pfui_post_admin_setup();
		}
	}

	new Penci_Gutenberg_Post_Format();
endif;