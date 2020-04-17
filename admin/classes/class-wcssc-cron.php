<?php
if(!defined('ABSPATH')){
    exit;
}

if( !class_exists('WCSSC_Cron') ){

    class WCSSC_Cron {

        /*
         * Constructor
         */
        function __construct() {

			add_action( 'wcssc_settings_saved', array($this, 'wcssc_bind_cron') );
			add_action( 'wcssc_clear_carts', array($this, 'wcssc_clear_carts') );
        }

		/*
		 * Binds the cron once settings are saved.
		 */
		function wcssc_bind_cron() {

			wp_schedule_event( strtotime("now"), 'hourly', 'wcssc_clear_carts' );
		}
		
		/*
		 * Clears the saved cart items.
		 */
		function wcssc_clear_carts(){

			global $wcssc;

			$interval = !empty($wcssc->configuration['wcssc_ip_interval']) ? $wcssc->configuration['wcssc_ip_interval'] : 7;
			$date_before_string = sprintf( _n( '%d day ago', '%d days ago', $interval ), $interval );

			$chunk = apply_filters( 'wcssc_clear_cart_chunk_size', 30 );

			$args = array(
				'post_type' => 'wcssc-cart',
				'post_status' => 'formed',
				'posts_per_page' => $chunk,
				'date_query' => array(
					'column' => 'post_modified_gmt',
					'before'  => $date_before_string,
				)
			);

			$shared_carts = new WP_Query($args);

			if($shared_carts->have_posts()){

				while($shared_carts->have_posts()){
					$shared_carts->the_post();
					wp_delete_post(  get_the_ID(), true );
				}
				wp_reset_postdata();
			}

		}
    }
}