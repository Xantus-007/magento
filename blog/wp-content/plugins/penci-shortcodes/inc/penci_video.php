<?php
/* ------------------------------------------------------- */
/* Video
/* ------------------------------------------------------- */
if ( ! function_exists( 'penci_penci_video_shortcode' ) ) {
	function penci_penci_video_shortcode( $atts, $content ) {
		extract( shortcode_atts( array(
			'width' => '500',
			'align' => 'center',
			'url' => ''
		), $atts ) );

		$return = '';
		if( ! $width || ! is_numeric($width) ): $width = '500'; endif;
		if( ! in_array( $align, array( 'left', 'right', 'center' ) ) ): $align = 'center'; endif;
		
		$return = '<div class="penci_video_shortcode video-align-'. $align .'" style="max-width: '. $width .'px">'. do_shortcode('[embed]'. $url .'[/embed]') .'</div>';

		return $return;
	}
}