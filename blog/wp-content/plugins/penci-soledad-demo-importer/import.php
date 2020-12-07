<?php
/**
 * Register one click import demo data
 */


add_filter( 'penci_soledad_demo_packages', 'penci_soledad_addons_import_register' );

function penci_soledad_addons_import_register() {
	$demo_listing = array(
		'default' => 'Default',
		'adventure-blog' => 'Adventure Blog',
		'animal-news' => 'Animal News',
		'architecture' => 'Architecture',
		'art-artist-blog' => 'Art Artist Blog',
		'art-magazine' => 'Art Magazine',
		'baby' => 'Baby',
		'beauty' => 'Beauty',
		'beauty-blog2' => 'Beauty Blog 2',
		'bitcoin-news' => 'Bitcoin News',
		'book' => 'Book',
		'breaking-news' => 'Breaking News',
		'business-magazine' => 'Business Magazine',
		'business-news' => 'Business News',
		'cars' => 'Cars',
		'charity' => 'Charity',
		'classic' => 'Classic',
		'coffee-blog' => 'Coffee Blog',
		'construction' => 'Construction',
		'cosmetic-blog' => 'Cosmetic Blog',
		'craft-diy-blog2' => 'Craft DIY Blog 2',
		'craft-diy' => 'Craft Diy',
		'dark-version' => 'Dark Version',
		'designers-blog' => 'Designers Blog',
		'education-news' => 'Education News',
		'elegant-blog' => 'Elegant Blog',
		'entertainment' => 'Entertainment',
		'environment-charity-blog' => 'Environment Charity Blog',
		'factory-news' => 'Factory News',
		'fashion-blog2' => 'Fashion Blog 2',
		'fashion-lifestyle' => 'Fashion Lifestyle',
		'fashion-magazine' => 'Fashion Magazine',
		'fashion-magazine2' => 'Fashion Magazine 2',
		'fitness' => 'Fitness',
		'fitness-blog' => 'Fitness Blog',
		'food' => 'Food',
		'food-blog2' => 'Food Blog 2',
		'food-news' => 'Food News',
		'gardening-blog' => 'Gardening Blog',
		'gardening-magazine' => 'Gardening Magazine',
		'game' => 'Game',
		'game-blog' => 'Game Blog',
		'hair-stylist-blog' => 'Hair Stylist Blog',
		'hair-style-magazine' => 'Hair Style Magazine',
		'health-medical' => 'Health Medical',
		'healthy-clean-eating-blog' => 'Healthy Clean Eating Blog',
		'hipster' => 'Hipster',
		'interior-design-blog' => 'Interior Design Blog',
		'interior-design-magazine' => 'Interior Design Magazine',
		'lawyers-blog' => 'Lawyears Blog',
		'magazine' => 'Magazine',
		'men-health-magazine' => 'Men Health Magazine',
		'minimal-simple-magazine' => 'Minimal Simple Magazine',
		'movie' => 'Movie',
		'old-fashioned-blog' => 'Old Fashioned Blog',
		'pet' => 'Pet',
		'pet-blog' => 'Pet Blog',
		'photographer' => 'Photographer',
		'photography-blog' => 'Photography Blog',
		'photography-magazine' => 'Photography Magazine',
		'radio-blog' => 'Radio Blog',
		'seo-blog' => 'SEO Blog',
		'science-news' => 'Science News',
		'seo-magazine' => 'Seo Magazine',
		'simple' => 'Simple',
		'spa-blog' => 'Spa Blog',
		'sport' => 'Sport',
		'sport-2' => 'Sport 2',
		'stylist-blog' => 'Stylist Blog',
		'tech-news' => 'Tech News',
		'technology' => 'Technology',
		'technology-blog2' => 'Technology Blog 2',
		'time-magazine' => 'Time Magazine',
		'travel' => 'Travel',
		'travel-blog2' => 'Travel Blog 2',
		'travel-blog3' => 'Travel Blog 3',
		'travel-guide-magazine' => 'Travel Guide Magazine',
		'travel-magazine' => 'Travel Magazine',
		'vegan-magazine' => 'Vegan Magazine',
		'video' => 'Video',
		'video-dark' => 'Video Dark',
		'videos-blog' => 'Videos Blog',
		'vintage-blog' => 'Vintage Blog',
		'viral' => 'Viral',
		'wedding' => 'Wedding',
		'music' => 'Music',
		'beauty-blog3' => 'Beauty Blog 3',
		'book-magazine' => 'Book Magazine',
		'car-blog' => 'Car Blog',
		'coding-blog' => 'Coding Blog',
		'colorful-magazine' => 'Clorfull Magazine',
		'dentist-blog' => 'Dentist Blog',
		'design-magazine' => 'Design Magazine',
		'fashion-blog3' => 'Fashion Blog 3',
		'freelancer-blog' => 'Freelancer Blog',
		'game-magazine' => 'Game Magazine',
		'handmade-blog' => 'Handmade Blog',
		'ios-tips-mag' => 'IOS Tips Magazine',
		'motorcycle-blog' => 'Motorcycle Blog',
		'musicband-blog' => 'Musicband Blog',
		'painter-blog' => 'Painter Blog',
		'software-tips-blog' => 'Software Tips Blog',
		'transport-blog' => 'Transport Blog',
		'vertical-nav' => 'Vertical Nav',
		'vertical-nav-dark' => 'Vertical Nav Dark',
		'video-blog2' => 'Video Blog 2',
	);
	
	asort( $demo_listing );
	
	$demo_configs = array();
	foreach ( $demo_listing as $key => $label ) {
		$config = array(
			'name'       => $label,
			'content'    => 'http://soledad.pencidesign.com/demodata/'. $key .'/demo-content.xml',
			'widgets'    => 'http://soledad.pencidesign.com/demodata/'. $key .'/widgets.wie',
			'preview'    => 'http://soledad.pencidesign.com/demodata/'. $key .'/preview.jpg',
			'customizer' => 'http://soledad.pencidesign.com/demodata/'. $key .'/customizer.dat',
			'menus'      => array( 'main-menu'   => 'menu-1' ),
		);
		if ( $key == 'default' ) {
			$config['pages'] = array(
				'front_page' 		=> '',
				'blog'       		=> '',
				'shop'       		=> 'Shop',
				'cart'       		=> 'Cart',
				'checkout'   		=> 'Checkout',
				'my_account' 		=> 'My Account',
				'portfolio'  		=> 'Masonry 3 Columns',
			);
			$config['options'] = array(
				'shop_catalog_image_size'   => array(
					'width'  => 600,
					'height' => 732,
					'crop'   => 1,
				),
				'shop_single_image_size'    => array(
					'width'  => 600,
					'height' => 732,
					'crop'   => 1,
				),
				'shop_thumbnail_image_size' => array(
					'width'  => 150,
					'height' => 183,
					'crop'   => 1,
				),
			);
		}else {
			$config['pages'] = array(
				'front_page' 		=> '',
				'blog'       		=> '',
			);
			$config['options'] = array();
		}

		// Add menu
		if ( $key == 'magazine' ) {
			$config['menus']['topbar-menu'] = 'top-bar-menu';
		} elseif ( $key == 'sport' ) {
			$config['menus']['topbar-menu'] = 'top-bar-menu';
		} elseif ( $key == 'video' ) {
			$config['menus']['topbar-menu'] = 'topbar-menu';
		} elseif ( $key == 'game' ) {
			$config['menus']['topbar-menu'] = 'top-bar-menu';
		} elseif ( $key == 'music' ) {
			$config['menus']['topbar-menu'] = 'top-bar-menu';
		} elseif ( $key == 'health-medical' ) {
			$config['menus']['topbar-menu'] = 'topbar-menu';
		} elseif ( $key == 'cars' ) {
			$config['menus']['footer-menu'] = 'footer-menu';
		} elseif ( $key == 'wedding' ) {
			$config['menus']['topbar-menu'] = 'top-bar-menu';
		} elseif ( $key == 'simple' ) {
			$config['menus']['topbar-menu'] = 'topbar-menu';
		} elseif ( $key == 'tech-news' ) {
			$config['menus']['topbar-menu'] = 'top-bar-menu';
		} elseif ( $key == 'business-news' ) {
			$config['menus']['footer-menu'] = 'footer-menu';
		} elseif ( $key == 'fashion-magazine' ) {
			$config['menus']['topbar-menu'] = 'top-bar-menu';
		} elseif ( $key == 'charity' ) {
			$config['menus']['topbar-menu'] = 'top-bar-menu';
		}


		$demo_configs[] = $config;
	}
	return $demo_configs;
}

add_action( 'penci_soledaddi_after_setup_pages', 'penci_soledad_addons_import_order_tracking' );

/**
 * Update more page options
 *
 * @param $pages
 */
function penci_soledad_addons_import_order_tracking( $pages ) {
	if ( isset( $pages['order_tracking'] ) ) {
		$order = get_page_by_title( $pages['order_tracking'] );

		if ( $order ) {
			update_option( 'penci_soledad_order_tracking_page_id', $order->ID );
		}
	}

	if ( isset( $pages['portfolio'] ) ) {
		$portfolio = get_page_by_title( $pages['portfolio'] );

		if ( $portfolio ) {
			update_option( 'penci_soledad_portfolio_page_id', $portfolio->ID );
		}
	}
}

add_action( 'penci_soledaddi_before_import_content', 'penci_soledad_addons_import_product_attributes' );

/**
 * Prepare product attributes before import demo content
 *
 * @param $file
 */
function penci_soledad_addons_import_product_attributes( $file ) {
	global $wpdb;

	if ( ! class_exists( 'WXR_Parser' ) ) {
		require_once WP_PLUGIN_DIR . '/penci-soledad-demo-importer/includes/parsers.php';
	}

	$parser      = new WXR_Parser();
	$import_data = $parser->parse( $file );

	if ( isset( $import_data['posts'] ) ) {
		$posts = $import_data['posts'];

		if ( $posts && sizeof( $posts ) > 0 ) {
			foreach ( $posts as $post ) {
				if ( 'product' === $post['post_type'] ) {
					if ( ! empty( $post['terms'] ) ) {
						foreach ( $post['terms'] as $term ) {
							if ( strstr( $term['domain'], 'pa_' ) ) {
								if ( ! taxonomy_exists( $term['domain'] ) ) {
									$attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $term['domain'] ) );

									// Create the taxonomy
									if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
										$attribute = array(
											'attribute_label'   => $attribute_name,
											'attribute_name'    => $attribute_name,
											'attribute_type'    => 'select',
											'attribute_orderby' => 'menu_order',
											'attribute_public'  => 0
										);
										$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
										delete_transient( 'wc_attribute_taxonomies' );
									}

									// Register the taxonomy now so that the import works!
									register_taxonomy(
										$term['domain'],
										apply_filters( 'woocommerce_taxonomy_objects_' . $term['domain'], array( 'product' ) ),
										apply_filters( 'woocommerce_taxonomy_args_' . $term['domain'], array(
											'hierarchical' => true,
											'show_ui'      => false,
											'query_var'    => true,
											'rewrite'      => false,
										) )
									);
								}
							}
						}
					}
				}
			}
		}
	}
}