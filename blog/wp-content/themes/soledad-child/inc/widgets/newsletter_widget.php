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
            $link = apply_filters( 'widget_link', $instance[ 'link' ] );
            $blog_title = get_bloginfo( 'name' );
            $tagline = get_bloginfo( 'description' );
            ?>
            <div class="NewsletterMailjet">
                <?php
                echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; ?>
                <p><?php echo $description; ?></p>
                <form class="MailjetForm">
                    <input class="form-control" required="" type="email" name="SubscriberEmail" placeholder="<?php echo esc_html__('E-mail adress','soledad'); ?>">
                    <div class="form-group">
                        <input class="form-control" id="infos_et_news" value="true" type="checkbox" name="infos_et_news">
                        <label for="infos_et_news"><?php echo esc_html__('Info and news','soledad'); ?></label>
                    </div>
                    <div class="form-group">
                        <input class="form-control" id="recettes" value="true" type="checkbox" name="recettes">
                        <label for="recettes"><?php echo esc_html__('Receipts','soledad'); ?></label>
                    </div>
                    <div class="form-group">
                        <input class="form-control" id="offres_promo" value="true" type="checkbox" name="offres_promo">
                        <label for="offres_promo"><?php echo esc_html__('Promo offers','soledad'); ?></label>
                    </div>
                    <div class="form-group">
                        <input class="form-control" required="" id="rgpd" value="true" type="checkbox" name="rgpd">
                        <label for="rgpd"><?php printf( __('I agree to receive newsletters from monbento and that my data will be processed in accordance with the <a href="%s" target="blank"> monbento personal data management policy. </a> I will unsubscribe at any time.', 'soledad' ), $link );?></label>
                    </div>
                    <div class="form-group">
                        <?php do_action('google_invre_render_widget_action'); ?>
                    </div>
                    <input class='SendA' type="button" value="<?php echo esc_html__('Inscription','soledad'); ?>">
                </form>

                <?php echo $args['after_widget']; ?>
            </div>
            <?php
        }

        public function form( $instance ) 
        {
            $title = ! empty( $instance['title'] ) ? $instance['title'] : ''; 
            $description = ! empty( $instance['description'] ) ? $instance['description'] : ''; 
            $link = ! empty( $instance['link'] ) ? $instance['link'] : ''; ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'description' ); ?>">description:</label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" value="<?php echo esc_attr( $description ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'link' ); ?>">link:</label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" value="<?php echo esc_attr( $link ); ?>" />
            </p>
            <?php 
        }

        public function update( $new_instance, $old_instance ) 
        {
            $instance = $old_instance;
            $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
            $instance[ 'description' ] = strip_tags( $new_instance[ 'description' ] );
            $instance[ 'link' ] = strip_tags( $new_instance[ 'link' ] );
            return $instance;
        }
    }
}