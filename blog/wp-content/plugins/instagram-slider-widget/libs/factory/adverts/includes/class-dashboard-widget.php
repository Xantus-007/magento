<?php

namespace WBCR\Factory_Adverts_102;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adverts Dashboard Widget.
 *
 * Adds a widget with a banner or a list of news.
 *
 * @author        Alexander Vitkalov <nechin.va@gmail.com>
 * @since         1.0.0 Added
 * @package       factory-adverts
 * @copyright (c) 2019 Webcraftic Ltd
 */
class Dashboard_Widget {

	/**
	 * Контент, который должен быть напечатан внутри дашбоард виджета
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 * @var string
	 */
	private $content;

	/**
	 * Экзепляр плагина с которым взаимодействует этот модуль
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 * @var \Wbcr_Factory420_Plugin
	 */
	private $plugin;

	/**
	 * Dashboard_Widget constructor.
	 *
	 * Call parent constructor. Registration hooks.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param \Wbcr_Factory420_Plugin $plugin
	 * @param string                  $content
	 */
	public function __construct( \Wbcr_Factory420_Plugin $plugin, $content ) {

		$this->plugin  = $plugin;
		$this->content = $content;

		if ( ! empty( $this->content ) ) {
			add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widgets' ], 999 );
		}
	}

	/**
	 * Add the News widget to the dashboard.
	 *
	 * @since 1.0.0 Added
	 */
	public function add_dashboard_widgets() {
		global $wp_meta_boxes;

		$widget_id = 'wbcr-factory-adverts-widget';

		wp_add_dashboard_widget( $widget_id, $this->plugin->getPluginTitle() . ' News', [
			$this,
			'print_widget_content'
		] );

		# Set dashboard widget first in order
		$normal_core   = $wp_meta_boxes['dashboard']['normal']['core'];
		$widget_backup = [ $widget_id => $normal_core[ $widget_id ] ];
		unset( $normal_core[ $widget_id ] );
		$sorted_core = array_merge( $widget_backup, $normal_core );

		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_core;
	}

	/**
	 * Create the function to output the contents of the Dashboard Widget.
	 *
	 * @since 1.0.0 Added
	 */
	public function print_widget_content() {
		?>
        <div class="wordpress-news hide-if-no-js">
            <div class="rss-widget">
				<?php echo $this->content; ?>
            </div>
        </div>
		<?php

	}
}
