<?php

if(!defined('ABSPATH')){
    exit;
}

if( !class_exists('WCSSC_Saved_Cart') ) {

	class WCSSC_Saved_Cart {

		/**
		 * WCSSC_Saved_Cart constructor.
		 */
		function __construct() {
			add_action( 'template_redirect', array($this, 'load_cart') );
		}

		/**
		 * Returns the URL to load saved cart.
		 *
		 * @param int $cart_id
		 *
		 * @return string|void URL to load a certain cart.
		 */
		function get_load_cart_link( $cart_id = null ){
			
			if( empty($cart_id) && !is_singular('wcssc-cart') ){
				return;
			}

			$cart_id = empty($cart_id) ? get_the_ID() : $cart_id;
			
			return wp_nonce_url( get_permalink($cart_id), 'load_wcssc_cart', 'load_wcssc_cart');
		}

		/**
		 * Loads the cart in Woocommerce store.
		 */
		function load_cart() {

			global $wcssc, $wpdb, $woocommerce, $post;

			if ( is_singular( 'wcssc-cart' ) && isset( $_GET['load_wcssc_cart'] ) && wp_verify_nonce( $_GET['load_wcssc_cart'], 'load_wcssc_cart' ) ) {

				$cart_data = get_post_meta( $post->ID, 'wcssc_cart_data' );
				$cart_data = $cart_data[0];

				if ( ! empty( $cart_data ) ) {

					$cart_page = wc_get_page_id( 'cart' );
					$woocommerce->cart->empty_cart();

					foreach ( $cart_data as $key => $item ) {
						$woocommerce->cart->add_to_cart( $item['product_id'], $item['quantity'], $item['variation_id'], $item['variation'] );
					}

					wp_redirect( get_permalink( $cart_page ) );
					die( 1 );
				}

			}
		}

		/**
		 * Saves the cart in 'formed' custom status.
		 *
		 * @param array $args Saved cart arguments.
		 *
		 * @return false|string Permalink for saved cart.
		 */
        function get_cart_link( $args = array() ) {

            global $wcssc;

            $data = WC()->cart->cart_contents;
			$cart_hash = $_COOKIE['woocommerce_cart_hash'];

			$post_exists = $this->get_cart_by_hash( array('name'=>$cart_hash) );

			if( empty($post_exists) ){

				$query_args = wp_parse_args($args,
					array(
						'post_title' => __( 'Shared cart - '.$cart_hash, 'wcssc' ),
						'post_name' => $cart_hash,
						'meta_input' => array(
							'wcssc_cart_data' => $data
						),
						'post_author' => $wcssc->settings['wcssc_user'],
						'post_status' => 'formed',
						'post_type' => 'wcssc-cart'
					)
				);

				$post_id = wp_insert_post( $query_args );

			}else{

				$post_id = $post_exists[0]->ID;
			}

            return get_permalink($post_id);
        }

		/**
		 * Get saved cart by woocommerce_cart_hash value. Hash is present as
		 * post slug for saved carts.
		 *
		 * @param array $args Arguments for getting stored cart.
		 *
		 * @return array|int[]|WP_Post[]
		 */
		function get_cart_by_hash( $args = array() ) {

			if ( empty( $args['name'] ) ) {
				return array();
			}

			$defaults = array(
				'post_type'        => 'wcssc-cart',
				'post_status'      => array( 'publish', 'formed' ),
				'suppress_filters' => false,
				'numberposts'      => 1
			);

			$r = wp_parse_args( $args, $defaults );

			return get_posts( $r );

		}

	}
}