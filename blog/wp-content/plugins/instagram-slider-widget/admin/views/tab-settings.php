<?php global $form; ?>

<div class="wrap">
    <div class="factory-bootstrap-421 factory-fontawesome-000">
        <h3><?php _e( 'Settings', 'insert-php' ) ?></h3>
        <div class="row">
            <div class="col-md-9">
                <form method="post" class="form-horizontal">
					<?php if ( ! empty( $wbcr_saved ) ) { ?>
                        <div id="message" class="alert alert-success">
                            <p><?php _e( 'The settings have been updated successfully!', 'insert-php' ) ?></p>
                        </div>
					<?php } ?>
                    <div style="padding-top: 10px;">
						<?php $form->html(); ?>
                    </div>
                    <div class="form-group form-horizontal">
                        <label class="col-sm-2 control-label"> </label>
                        <div class="control-group controls col-sm-10">
							<?php wp_nonce_field( $this->plugin->getPrefix() . 'settings_form', $this->plugin->getPrefix() . 'nonce' ); ?>
                            <input name="<?php echo $this->plugin->getPrefix() . 'saved' ?>" class="btn btn-primary" type="submit" value="<?php _e( 'Save settings', 'insert-php' ) ?>"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-3">
                <div id="wapt-dashboard-widget" class="wapt-right-widget">
					<?php
					global $wbcr_isw_adinserter;
					echo $wbcr_isw_adinserter->get_adverts( 'right_sidebar' );
                    //apply_filters( 'wbcr/inp/dashboard/widget/print', '' );
					?>
                </div>
            </div>
        </div>
    </div>
</div>