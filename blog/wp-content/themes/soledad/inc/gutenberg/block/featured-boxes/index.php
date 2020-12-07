<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(  ! class_exists( 'Penci_Gutenberg_Soledad_Featured_Boxes' ) ):
class Penci_Gutenberg_Soledad_Featured_Boxes {

	public function render( $attributes, $content ) {

		$featured_boxes = array();
		$list_key    = array( 'box_1_', 'box_2_', 'box_3_', 'box_4_', 'box_5_', 'box_6_', 'box_7_', 'box_8_' );

		if ( ! $attributes ) {
			return;
		}

		foreach ( $list_key as $key ){
			if( isset( $attributes[ $key . 'img'] ) && $attributes[ $key . 'img'] ){
				$featured_boxes[ $key ] = array(
					'img'  => $attributes[ $key . 'img'],
					'text' => isset( $attributes[ $key . 'text'] ) ? $attributes[ $key . 'text'] : '',
					'url'  => isset( $attributes[ $key . 'url'] ) ? $attributes[ $key . 'url'] : '',
				);
			}
		}


		if ( ! $featured_boxes ) {
			$mess = esc_html__( 'Please fill Featured Boxes', 'soledad' );
			return '<div class="penci-wpblock">' . Penci_Soledad_Gutenberg::message( 'Penci Featured Boxes', $mess ) . '</div>';
		}

		$style         = isset( $attributes['style'] ) ? $attributes['style'] : 'boxes-style-1';
		$columns       = isset( $attributes['columns'] ) ? $attributes['columns'] : 'boxes-3-columns';
		$size          = isset( $attributes['size'] ) ? $attributes['size'] : 'horizontal';
		$new_tab       = isset( $attributes['new_tab'] ) ? $attributes['new_tab'] : 'no';
		$margin_top    = isset( $attributes['margin_top'] ) ? $attributes['margin_top'] : '0';
		$margin_bottom = isset( $attributes['margin_bottom'] ) ? $attributes['margin_bottom'] : '0';

		$style_css   = ' style="margin-top: ' . $margin_top . 'px; margin-bottom: ' . $margin_bottom . 'px;"';
		$weight_text = get_theme_mod( 'penci_home_box_weight' ) ? get_theme_mod( 'penci_home_box_weight' ) : 'normal';
		$thumb       = 'penci-thumb';
		if ( $size == 'square' ) {
			$thumb = 'penci-thumb-square';
		} elseif ( $size == 'vertical' ) {
			$thumb = 'penci-thumb-vertical';
		}
		ob_start();
		?>
		<div class="container home-featured-boxes home-featured-boxes-vc boxes-weight-<?php echo $weight_text; ?> boxes-size-<?php echo $size; ?>"<?php echo $style_css; ?>>
			<ul class="homepage-featured-boxes <?php echo $columns; ?>">
				<?php
				foreach ( $featured_boxes as $item ) {
					$homepage_box_image = esc_url( $item['img'] );
					$homepage_box_text = $item['text'];
					$homepage_box_url = $item['url'];

					$open_url  = '';
					$close_url = '';
					$target = '';
					if( 'yes' == $new_tab ):
						$target = ' target="_blank"';
					endif;
					if ( $homepage_box_url ) {
						$open_url  = '<a href="' . do_shortcode( $homepage_box_url ) . '"' . $target . '>';
						$close_url = '</a>';
					}
					?>
					<li class="penci-featured-ct">
						<?php echo wp_kses( $open_url, penci_allow_html() ); ?>
						<div class="penci-fea-in <?php echo $style; ?>">
							<?php if( ! get_theme_mod( 'penci_disable_lazyload_layout' ) ) { ?>
								<div class="fea-box-img penci-image-holder penci-holder-load penci-lazy" data-src="<?php echo penci_get_image_size_url( $homepage_box_image, $thumb ); ?>"></div>
							<?php } else { ?>
								<div class="fea-box-img penci-image-holder" style="background-image: url('<?php echo penci_get_image_size_url( $homepage_box_image, $thumb ); ?>');"></div>
							<?php }?>

							<?php if( $homepage_box_text ): ?>
								<h4><span class="boxes-text"><span style="font-weight: <?php echo $weight_text; ?>"><?php echo do_shortcode( $homepage_box_text ); ?></span></span></h4>
							<?php endif; ?>
						</div>
						<?php echo wp_kses( $close_url, penci_allow_html() ) ; ?>
					</li>
					<?php
				}
				?>
			</ul>
		</div><!--endpenci-block-->
		<?php
		$shortcode_content = ob_get_clean();

		$output = '<div class="penci-wpblock">';
		$output .= Penci_Soledad_Gutenberg::message( 'Penci Featured Boxes', esc_html__( 'Click to edit this block', 'soledad' ) );
		$output .= $shortcode_content;
		$output .= '</div>';

		return $output;

	}
	public function attributes() {
		$options = array(
			'style'         => array( 'type' => 'string', 'default' => 'boxes-style-1', ),
			'columns'       => array( 'type' => 'string', 'default' => 'boxes-3-columns', ),
			'size'          => array( 'type' => 'string', 'default' => 'horizontal', ),
			'new_tab'       => array( 'type' => 'string', 'default' => 'no', ),
			'margin_top'    => array( 'type' => 'number', 'default' => '0', ),
			'margin_bottom' => array( 'type' => 'number', 'default' => '0', ),

			'box_1_img'  => array( 'type' => 'string' ),
			'box_1_text' => array( 'type' => 'string' ),
			'box_1_url'  => array( 'type' => 'string' ),
			'box_2_img'  => array( 'type' => 'string' ),
			'box_2_text' => array( 'type' => 'string' ),
			'box_2_url'  => array( 'type' => 'string' ),
			'box_3_img'  => array( 'type' => 'string' ),
			'box_3_text' => array( 'type' => 'string' ),
			'box_3_url'  => array( 'type' => 'string' ),
			'box_4_img'  => array( 'type' => 'string' ),
			'box_4_text' => array( 'type' => 'string' ),
			'box_4_url'  => array( 'type' => 'string' ),
			'box_5_img'  => array( 'type' => 'string' ),
			'box_5_text' => array( 'type' => 'string' ),
			'box_5_url'  => array( 'type' => 'string' ),
			'box_6_img'  => array( 'type' => 'string' ),
			'box_6_text' => array( 'type' => 'string' ),
			'box_6_url'  => array( 'type' => 'string' ),
			'box_7_img'  => array( 'type' => 'string' ),
			'box_7_text' => array( 'type' => 'string' ),
			'box_7_url'  => array( 'type' => 'string' ),
			'box_8_img'  => array( 'type' => 'string' ),
			'box_8_text' => array( 'type' => 'string' ),
			'box_8_url'  => array( 'type' => 'string' ),
		);

		return $options;
	}
}
endif;