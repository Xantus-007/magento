<style>

    .wis-section-wrapper {
        width: 100%;
        margin-top: 10px;
    }

    .wis-section {
        padding: 29px 29px 29px 29px;
    }

    .wis-section .container {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        margin-right: auto;
        margin-left: auto;
        position: relative;
        max-width: 1140px;
        /*min-height: 600px;*/
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
    }

    .wis-section-intro {
        background-image: url('<?php echo WIS_PLUGIN_URL;?>/admin/assets/img/fon.jpg');
        background-position: bottom center;
        background-size: cover;
        box-shadow: 0px 0px 34px 0px rgba(107, 107, 107, 0.5);
        transition: background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;
        text-align: center;
    }

    .wis-section-intro .container h2 {
        font-size: 61px;
        font-weight: 500;
        text-transform: uppercase;
        line-height: 1.1em;
        color: #fff;
        text-align: center;
    }

    .wis-section-intro .container p {
        margin-bottom: 1.6em;
        color: #fffcfc;
        font-family: "Arial", Sans-serif;
        font-size: 22px;
        line-height: 1.3em;
        letter-spacing: 1.1px;
    }

    .wis-section-changelog h4 {
        font-size: 1.3333333333333rem;
    }

    .wis-section-changelog p,
    .wis-section-changelog ul > li {
        font-size: 15px;
    }

    .wis-section-changelog ul {
        list-style: inherit;
        margin-left: 40px;
    }

    #wpfooter {
        position: relative !important;
    }


</style>

<div class="wis-section-wrapper">
    <section class="wis-section wis-section-intro">
        <div class="container">

            <div>
                <h2><?php esc_html_e( 'Social Slider Widget', 'instagram-slider-widget' ) ?></h2>

                <p><?php esc_html_e( 'We didn’t please you with updated lately. However, great news today! We are about to tell you about all the spectacular changes that are planned for our plugin!', 'instagram-slider-widget' ) ?></p>

                <p><?php echo __( 'First of all, we proudly announce that a new group of developers, <span style="text-decoration: underline;"><strong>Creative Motion</strong></span>, are helping us with plugin improvement.', 'instagram-slider-widget' ) ?></p>

                <p><?php esc_html_e( 'Auto Post Thumbnails has perfectly fit in our close family of popular plugins with more than 600,000 users worldwide.', 'instagram-slider-widget' ) ?></p>

                <p><?php esc_html_e( 'What you can expect soon:', 'instagram-slider-widget' ) ?></p>

            </div>
        </div>

    </section>

    <section class="wis-section wis-section-changelog">
        <div class="container">
            <div>
                <h4>1.4.4</h4>
                <p><?php echo __( 'Bug fixes', 'instagram-slider-widget' ); ?></p>
            </div>
        </div>
    </section>
</div>