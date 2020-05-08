<?php
if(!defined('ABSPATH')){
    exit;
}

if( !class_exists('WCSSC_AJAX') ){

    class WCSSC_AJAX {

        //Constructor
        function __construct() {
            //Generate dialog html
            add_action( 'wp_ajax_wcssc_share_dialog_html', array($this, 'generate_dialog_html') );
            add_action( 'wp_ajax_nopriv_wcssc_share_dialog_html', array($this, 'generate_dialog_html') );

            //Generate email html in dialog.
            add_action( 'wp_ajax_wcssc_generate_email_html', array($this, 'generate_email_html') );
            add_action( 'wp_ajax_nopriv_wcssc_generate_email_html', array($this, 'generate_email_html') );

            //Get ready mail and make it ready to send.
            add_action( 'wp_ajax_wcssc_send_mail', array($this, 'send_mail') );
            add_action( 'wp_ajax_nopriv_wcssc_send_mail', array($this, 'send_mail') );

            //Save box html generation.
            add_action( 'wp_ajax_nopriv_wcssc_generate_save_html', array($this, 'generate_save_html') );
            add_action( 'wp_ajax_wcssc_generate_save_html', array($this, 'generate_save_html') );
            
            //Save the carts
            add_action( 'wp_ajax_wcssc_save_cart', array($this, 'save_cart') );
        }

        /*
         * 
         */
        function generate_dialog_html(){
            global $wcssc, $wcssc_saved_cart;
            $cart_link = $wcssc_saved_cart->get_cart_link();

            ob_start(); ?>

                <ul id="wcssc-share-box-inner">

                    <?php if( !empty($wcssc->configuration['wcssc_sm_fb']) && 'yes' == $wcssc->configuration['wcssc_sm_fb'] ){?>
                        <li class="wcssc-icon-holder">
                            <a target="_blank" href="<?php echo wcssc_social_media_url( 'facebook', $cart_link ); ?>">
                                <i class="fab fa-facebook"></i>
                                <span><?php _e( 'Facebook', 'wcssc' ); ?></span>
                            </a>
                        </li><?php
                    } ?>

                    <?php if( !empty($wcssc->configuration['wcssc_sm_tw']) && 'yes' == $wcssc->configuration['wcssc_sm_tw'] ){?>
                        <li class="wcssc-icon-holder">
                            <a target="_blank" href="<?php echo wcssc_social_media_url( 'twitter', $cart_link ); ?>">
                                <i class="fab fa-twitter"></i>
                                <span><?php _e( 'Twitter', 'wcssc' ); ?></span>
                            </a>
                        </li><?php
                    } ?>

                    <?php if( !empty($wcssc->configuration['wcssc_sm_wp']) && 'yes' == $wcssc->configuration['wcssc_sm_wp'] ){?>
                        <li class="wcssc-icon-holder">
                            <a target="_blank" href="<?php echo wcssc_social_media_url( 'whatsapp', $cart_link ); ?>">
                                <i class="fab fa-whatsapp"></i>
                                <span><?php _e( 'WhatsApp', 'wcssc' ); ?></span>
                            </a>
                        </li><?php
                    } ?>

                    <?php if( !empty($wcssc->configuration['wcssc_sm_skype']) && 'yes' == $wcssc->configuration['wcssc_sm_skype'] ){?>
                        <li class="wcssc-icon-holder">
                            <a target="_blank" href="<?php echo wcssc_social_media_url( 'skype', $cart_link ); ?>">
                                <i class="fab fa-skype"></i>
                                <span><?php _e( 'Skype', 'wcssc' ); ?></span>
                            </a>
                        </li><?php
                    } ?>

                    <?php if( !empty($wcssc->configuration['wcssc_sm_mail']) && 'yes' == $wcssc->configuration['wcssc_sm_mail'] ){?>
                        <li class="wcssc-icon-holder">
                            <a id="wcssc-mail" target="_blank" href="#">
                                <i class="fas fa-envelope"></i>
                                <span><?php _e( 'Email', 'wcssc' ); ?></span>
                            </a>
                        </li><?php
                    } ?>

                    <?php if( !empty($wcssc->configuration['wcssc_sm_clipboard']) && 'yes' == $wcssc->configuration['wcssc_sm_clipboard'] ){?>
                    <li class="wcssc-icon-holder">
                    <a id="wcssc-clipboard" target="_blank" href="#" data-msg="<?php _e( 'Link copied.', 'wcssc' ) ?>">
                        <i class="fas fa-copy"></i>
                        <span><?php _e( 'Copy to clipboard', 'wcssc' ); ?></span>
                        <input type="text" style="display: none;" id="wcssc-copy-link" value="<?php echo esc_url( $cart_link ) ?>" />
                    </a>
                    </li><?php
                    } ?>

	                <?php if( !empty($wcssc->configuration['wcssc_sm_save']) && 'yes' == $wcssc->configuration['wcssc_sm_save'] && is_user_logged_in() ){?>
                        <li class="wcssc-icon-holder">
                            <a id="wcssc-save" target="_blank" href="#">
                                <i class="fas fa-save"></i>
                                <span><?php _e( 'Save', 'wcssc' ); ?></span>
                            </a>
                        </li><?php
                    } ?>

                    <?php do_action( 'wcssc_social_items', $wcssc->configuration ); ?>

                </ul><?php

            echo apply_filters( 'wcssc_sharebox_markup', ob_get_clean() );
            die;
        }

        /*
         * 
         */
        function generate_email_html() {

			global $wcssc, $wcssc_saved_cart;

            $subject    = !empty($wcssc->configuration['wcssc_email_subject']) ? $wcssc->configuration['wcssc_email_subject'] : '';
            $body       = !empty($wcssc->configuration['wcssc_email_body']) ? $wcssc->configuration['wcssc_email_body'] : '';

			$body		= str_replace( '{cart_link}', $wcssc_saved_cart->get_cart_link(), $body );
			$body		= str_replace( '{blogname}', get_bloginfo('name'), $body );
			$body		= str_replace( '{siteurl}', get_bloginfo('url'), $body );

            ob_start();?>
            
            <div id="wcssc-share-box-inner">
                <form method="post" action="/">
                    <div class="sharebox-div">
                        <input placeholder="<?php _e('Email Address', 'wcssc') ?>" name="wcssc_mailto" type="email" class="input-text" id="wcssc-mailto" />
                    </div>
					
                    <div class="sharebox-div">
                        <input placeholder="<?php _e('Email Subject', 'wcssc') ?>" name="wcssc_subject" type="text" class="input-text" id="wcssc-subject" value="<?php echo $subject; ?>" />
                    </div>
                    <div class="sharebox-div">
                        <textarea placeholder="<?php _e('Email Content', 'wcssc') ?>" name="wcssc_body" class="input-text" id="wcssc-body" rows="5" cols="20"><?php echo $body; ?></textarea>
                    </div>
                    <div class="sharebox-div">
                        <button class="button button-primary" id="wcssc-send-mail"><?php _e('Send', 'wcssc'); ?></button>
                    </div>
                </form>
                <a class="wcssc-dialog-back" href="#"><?php _e('Back', 'wcssc'); ?></a>
            </div><?php
            
            echo apply_filters('wcssc_emailbox', ob_get_clean());
            die(1);
        }

        /*
         * Send mail
         */
        function send_mail(){
            global $wcssc;
            parse_str($_POST['data'], $data);

			$response = array(
				'error' => false,
				'msg' => __( 'Email sent successfully.', 'wcssc' )
			);

            $to			= $data['wcssc_mailto'];
            $subject	= !empty($data['wcssc_subject']) ? $data['wcssc_subject'] : ( !empty($wcssc->configuration['wcssc_email_subject']) ? $wcssc->configuration['wcssc_email_subject'] : __('Check this cart', 'wcssc') );
            $body		= !empty($data['wcssc_body']) ? $data['wcssc_body'] : ( !empty($wcssc->configuration['wcssc_email_body']) ? $wcssc->configuration['wcssc_email_body'] : sprintf( __( 'Check this cart: %s', 'wcssc' ),  $wcssc_saved_cart->get_cart_link() ) );

			//Change From Email Address
			apply_filters( 'wp_mail_from', function( $email ){
				            
				$from_email	= !empty($wcssc->configuration['wcssc_email_from']) ? $wcssc->configuration['wcssc_email_from'] : '';
				if( !empty($from_email) )
					$email = $from_email;
				
				return $email;

			});

			//Change From Email Name
			apply_filters( 'wp_mail_from_name', function( $name ){
				            
				$from_name	= !empty($wcssc->configuration['wcssc_email_from_name']) ? $wcssc->configuration['wcssc_email_from_name'] : '';
				if( !empty($from_name) )
					$name = $from_email;

				return $name;

			});

			//Useful for sending newsletters
			$filtered_email_body	= apply_filters( 'wcssc_email_body', $body );

			$mail_sent = wp_mail( $to, $subject, $filtered_email_body );

			if(!$mail_sent){

				$response = array(
					'error' => true,
					'msg' => __( 'Email couldn\'t be sent.', 'wcssc' )
				);

				wp_send_json_error($response);
			}

			wp_send_json_success($response);
        }

        /*
         * 
         */
        function generate_save_html() {
            
            ob_start();?>
            
            <div id="wcssc-share-box-inner">
                <form method="post" action="/">
                    <div class="sharebox-div">
                        <label for="wcssc-save-ip"><?php _e( 'Enter name for saved cart. This would be useful for future reference.', 'wcssc' ); ?></label>
                    </div>
                    <div class="sharebox-div">
                        <input name="wcssc_save_input" placeholder="<?php _e( 'Cart Title', 'wcssc' ) ?>" type="text" class="input-text" id="wcssc-save-ip" />
                    </div>
                    <div class="sharebox-div">
                        <textarea name="wcssc_cart_desc" placeholder="<?php _e( 'Cart Description', 'wcssc' ) ?>"></textarea>
                    </div>
                    <div class="sharebox-div">
                        <button class="button button-primary" id="wcssc-save-cart"><?php _e('Save', 'wcssc'); ?></button>
                    </div>
                </form>
                <a class="wcssc-dialog-back" href="#"><?php _e('Back', 'wcssc'); ?></a>
            </div><?php
            
            echo apply_filters('wcssc_savebox', ob_get_clean());
            die(1);
        }

	    /**
	     * Save carts for customers.
	     */
	    public function save_cart() {
		    global $wcssc_saved_cart;

		    parse_str( $_POST['data'], $data );

		    $response     = array();
		    $cart_hash    = $_COOKIE['woocommerce_cart_hash'];
		    $post_title   = esc_attr( $data['wcssc_save_input'] );
		    $post_content = esc_textarea( $data['wcssc_cart_desc'] );
		    $saved_cart   = $wcssc_saved_cart->get_cart_by_hash( array( 'name' => $cart_hash ) );

		    $update = wp_update_post( array(
			    'ID'           => $saved_cart[0]->ID,
			    'post_status'  => 'publish',
			    'post_title'   => $post_title,
			    'post_type'    => 'wcssc-cart',
			    'post_content' => $post_content
		    ) );

		    update_post_meta( $saved_cart[0]->ID, 'wcssc_has_title', 1 );

		    do_action( 'wcssc_cart_saved', $data, $saved_cart[0]->ID );

		    if ( empty( $update ) || ! is_wp_error( $update ) ) {
			    $response['msg'] = '<p style="text-align: center;">' . __( 'Cart saved successfully.', 'wcssc' ) . '</p>';
		    }

		    wp_send_json_success( $response );
	    }
    }
    
    new WCSSC_AJAX();
}