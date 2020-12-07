<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(  ! class_exists( 'Penci_Gutenberg_Soledad_Featured_Cat' ) ):
class Penci_Gutenberg_Soledad_Featured_Cat {

	public function render( $attributes, $content ) {


		if( ! isset(  $attributes['category'] ) || ! $attributes['category'] ){
			$mess = esc_html__( 'Please fill the category slug.', 'soledad' );
			return  '<div class="penci-wpblock">' . Penci_Soledad_Gutenberg::message( 'Penci Featured Cat', $mess ) . '</div>';
		}

		$param = '';
		if( $attributes ){
			foreach ( (array)$attributes as $k => $v ){
				if( $v ){
					$param .= ' ' . $k . '="' . $v . '"';
				}
			}
		}

		$output = '<div class="penci-wpblock">';
		$output .= Penci_Soledad_Gutenberg::message( 'Penci Featured Cat', esc_html__( 'Click to edit this block', 'soledad' ) );
		$output .= do_shortcode( '[featured_cat  ' . $param . ']' );
		$output .= '</div><!--endpenci-block-->';
		return $output;
	}

	public function attributes() {
	$options = array(
		'style'    => array(
			'type'    => 'string',
			'default' => 'style-1',
		),
		'category' => array(
			'type' => 'string',
		),
		'number'   => array(
			'type'    => 'number',
			'default' => '5',
		),
		'orderby'  => array(
			'type'    => 'string',
			'default' => 'date',
		),
		'order'    => array(
			'type'    => 'string',
			'default' => 'DESC',
		)
	);

	return $options;
}
}
endif;