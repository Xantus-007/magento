<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(  ! class_exists( 'Penci_Gutenberg_Soledad_Blockquote' ) ):
class Penci_Gutenberg_Soledad_Blockquote {

	public function render( $attributes, $content ) {
		if( ! function_exists( 'penci_blockquote_shortcode' ) ){
			$mess = esc_html__( 'Please active Penci Shortcodes plugin', 'soledad' );
			return  '<div class="penci-wpblock">' . Penci_Soledad_Gutenberg::message( 'Penci Blockquote', $mess ) . '</div>';
		}

		$param = ' wpblock="true"';
		if( $attributes ){
			foreach ( (array)$attributes as $k => $v ){
				if( $v && 'content' != $k ){
					$param .= ' ' . $k . '="' . $v . '"';
				}
			}
		}
		
		$block_style = get_theme_mod('penci_blockquote_style') ? get_theme_mod('penci_blockquote_style') : 'style-1';
		$output = '<div class="penci-wpblock blockquote-' . $block_style . '">';
		$output .= Penci_Soledad_Gutenberg::message( 'Penci Block Quote', esc_html__( 'Click to edit this block', 'soledad' ) );
		$output .=  do_shortcode( '[blockquote  ' . $param . ']' . ( $attributes['content'] ? $attributes['content'] : '' ) . '[/blockquote]' );
		$output .= '</div><!--endpenci-block-->';

		return $output;
	}
	public function attributes() {
		$options = array(
			'content' => array(
				'type'    => 'string',
				'default' => esc_html__( 'Add Quote Content...', 'soledad' ),
			),
			'author'  => array(
				'type'    => 'string',
				'default' => esc_html__( 'Add Quote Author...', 'soledad' ),
			),
			'align' => array(
				'type'    => 'none',
				'default' => ''
			),
		);

		return $options;
	}
}
endif;