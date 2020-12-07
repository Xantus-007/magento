<?php
remove_action( 'admin_footer', 'vc_loop_include_templates',1 );
add_action( 'wp_ajax_vc_edit_form', 'penci_remove_shortcode_param_loop', 1 );
if ( ! function_exists( 'penci_remove_shortcode_param_loop' ) ) {
	function penci_remove_shortcode_param_loop() {
		global $vc_params_list;

		$key = array_search( 'loop', $vc_params_list );
		if ( $key !== false ) {
			unset( $vc_params_list[ $key ] );
		}

	}
}

require get_template_directory() . '/inc/js_composer/params/loop/register.php';
vc_add_shortcode_param( "loop", "penci_soledad_vc_param_loop" );
