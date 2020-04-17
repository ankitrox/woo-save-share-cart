<?php
if(!defined('ABSPATH')){
    exit;
}

if( !class_exists('WCSSC_Rewrite_API') ){

    class WCSSC_Rewrite_API {

        //Constructor
        function __construct() {   
            add_filter( 'generate_rewrite_rules', array($this, 'share_cart_rules') );
            add_filter( 'query_vars', array($this, 'query_vars') );
            add_action( 'wp', array($this, 'redirect_to_cart') );

            //Woocommerce Endpoints
            add_action( 'init', array($this, 'saved_cart_custom_endpoint') );
            add_action( 'woocommerce_account_saved-carts_endpoint', array($this, 'saved_cart_endpoint_content') );
        }

        /*
         * Add rewrite rules for shared cart.
         */
        function share_cart_rules( $wp_rewrite ){

            $share_cart_rules = array(
                'share_cart/([A-Za-z0-9]+)?$' => 'index.php?shared_cart=$matches[1]',
                'saved-cart/([A-Za-z0-9]+)?$' => 'index.php?saved-cart=$matches[1]',
            );
            
            $wp_rewrite->rules = $share_cart_rules + $wp_rewrite->rules;
            return $wp_rewrite->rules;
        }

        /*
         * Add query var 'shared_cart' for holding cart id.
         */
        function query_vars($query_vars){
            
            $query_vars[] = 'shared_cart';
            $query_vars[] = 'saved-cart';
            $query_vars[] = 'saved-carts';
            return $query_vars;
        }

	    /**
	     * Load the products in cart and redirect user to cart page.
	     */
	    function redirect_to_cart() {
		    global $wcssc, $wpdb, $woocommerce;

		    $shared_cart = get_query_var( 'shared_cart' );

		    if ( ! empty( $shared_cart ) ) {

			    $sql       = $wpdb->prepare( "SELECT data FROM " . $wcssc->table_name . " WHERE fragment='%s'", $shared_cart );
			    $cart_data = $wpdb->get_col( $sql );//get cart data
			    $cart_data = wcssc_cart_contents( $cart_data );

			    if ( ! empty( $cart_data ) ) {

				    $cart_page = wc_get_page_id( 'cart' );
				    $woocommerce->cart->empty_cart();
				    $cart = maybe_unserialize( $cart_data['cart'] );

				    foreach ( $cart as $key => $item ) {
					    $woocommerce->cart->add_to_cart( $item['product_id'], $item['quantity'], $item['variation_id'], $item['variation'] );
				    }

				    wp_safe_redirect( get_permalink( $cart_page ) );
				    die( 1 );
			    }
		    }
	    }
        
        function saved_cart_custom_endpoint(){
            add_rewrite_endpoint( 'saved-carts', EP_ROOT | EP_PAGES );
        }

	    function saved_cart_endpoint_content() {
		    if ( ! locate_template( 'wcssc-cart/templates/template-saved-carts.php', true, true ) ) {
			    $default_template = WCSSC_BASE . '/templates/template-saved-carts.php';

			    $template_to_include = apply_filters( 'wcssc_saved_carts_account_template', $default_template );

			    if ( is_string( $template_to_include ) && file_exists( $template_to_include ) ) {
				    require_once "$template_to_include";
			    } else {
				    require_once "$default_template";
			    }
		    }
	    }
    }
}