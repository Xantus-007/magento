<?php
/**
 * About me widget
 * Display your information on footer or sidebar
 *
 * @package Wordpress
 * @since   1.0
 */

add_action( 'widgets_init', 'monbento_newsletter_widget' );

function monbento_newsletter_widget() {
	register_widget( 'Monbento_mailjet_widget' );
}
if( ! class_exists( 'Monbento_mailjet_widget' ) ) {
	class Monbento_mailjet_widget extends WP_Widget {

        public function __construct() 
        {
            $widget_options = array( 
                'classname' => 'Monbento_mailjet_widget',
                'description' => 'This is Widget for newsletter subscription form',
            );
            parent::__construct( 'Monbento_mailjet_widget', esc_html__( '.Monbento Mailjet', 'soledad' ), $widget_options );
        }

        public function widget( $args, $instance ) 
        {
            $title = apply_filters( 'widget_title', $instance[ 'title' ] );
            $description = apply_filters( 'widget_description', $instance[ 'description' ] );
            $blog_title = get_bloginfo( 'name' );
            $tagline = get_bloginfo( 'description' );
            echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; ?>
            <p><?php echo $description; ?></p>
           <form class="MailjetForm">
                <input class="form-control" type="email" name="SubscriberEmail">
                <input class="form-control" type="checkbox" name="offres_promo">
                <input class="form-control" type="checkbox" name="infos_et_news">
                <input class="form-control" type="checkbox" name="recettes">
                <?php do_action(‘google_invre_render_widget_action’); ?>
                <input class="form-control" type="submit" value="<?php echo esc_html__('Inscription','monbento'); ?>">
            </form>

            <?php echo $args['after_widget'];
        }

        public function form( $instance ) 
        {
            $title = ! empty( $instance['title'] ) ? $instance['title'] : ''; 
            $description = ! empty( $instance['description'] ) ? $instance['description'] : ''; ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
                <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'description' ); ?>">description:</label>
                <input type="text" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" value="<?php echo esc_attr( $description ); ?>" />
            </p>
            <?php 
        }

        public function update( $new_instance, $old_instance ) 
        {
            $instance = $old_instance;
            $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
            $instance[ 'description' ] = strip_tags( $new_instance[ 'description' ] );
            return $instance;
        }
    }
}