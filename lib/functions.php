<?php

/**
 * Generates the share URL for specific social media.
 */
if ( ! function_exists( 'wcssc_social_media_url' ) ) {

	function wcssc_social_media_url( $social_media = '', $cart_link = '' ) {

		global $wcssc;
		$is_mobile = wp_is_mobile();
		$url       = '';

		switch ( $social_media ) {

			case 'facebook':
				$url = add_query_arg( 'u', urlencode( $cart_link ), 'https://www.facebook.com/sharer.php' );
				break;

			case 'twitter':
				$url = add_query_arg( array( 'url' => urlencode( $cart_link ) ), 'https://twitter.com/intent/tweet' );
				break;

			case 'whatsapp':
				if ( ! $is_mobile ) {
					$url = add_query_arg( array( 'text' => urlencode( $cart_link ) ), 'https://web.whatsapp.com/send' );
				} else {
					$url = 'whatsapp://send?text=' . urlencode( $cart_link );
				}
				break;

			case 'skype':
				$url = add_query_arg( array( 'url' => urlencode( $cart_link ) ), 'https://web.skype.com/share' );
				break;

			default :
				$url = apply_filters( 'wcssc_social_media_' . $social_media, $cart_link, $wcssc->configuration );
				break;

		}

		return $url;
	}
}

/**
 * Get cart contents in unserialised forms.
 */
if ( ! function_exists( 'wcssc_cart_contents' ) ) {

	function wcssc_cart_contents( $cart_data ) {

		if ( ( ! empty( $cart_data ) && is_array( $cart_data ) ) ) {
			$cart_array = maybe_unserialize( $cart_data[0] );

			return $cart_array;
		} else {
			return false;
		}
	}
}

/**
 * Checks whether plugin scrips and styles should be loaded.
 *
 * @param $load bool
 *
 * @return $load bool
 */
if ( ! function_exists( 'wcssc_load_scripts' ) ) {

	function wcssc_load_scripts( $load ) {

		if ( is_cart() || is_singular( 'wcssc-cart' ) || ( get_queried_object_id() === wc_get_page_id( 'myaccount' ) ) ) {
			$load = true;
		}

		return $load;
	}

	add_filter( 'wcssc_load_scripts', 'wcssc_load_scripts', 10 );
}

if ( ! function_exists( 'wcssc_remove_saved_cart' ) ) {

	/**
	 * Remove a saved cart.
	 */
	function wcssc_remove_saved_cart() {
		$cart_remove       = filter_input( INPUT_GET, 'remove_cart', FILTER_VALIDATE_INT );
		$cart_remove_nonce = filter_input( INPUT_GET, 'wcssc_nonce', FILTER_SANITIZE_STRING );
		$referrer          = wp_get_referer();

		if ( ! empty( $cart_remove ) && ! empty( $cart_remove_nonce ) && wp_verify_nonce( $cart_remove_nonce, 'wcssc_nonce' ) ) {
			wp_delete_post( $cart_remove, true );
			header( "X-Redirect-By: WCSSC_Remove_Cart" );
		}
	}

	add_action( 'init', 'wcssc_remove_saved_cart' );
}

if ( ! function_exists( 'wcssc_show_message' ) ) {

	/**
	 * Show success or error message.
	 */
	function wcssc_show_message() {
		$headers_list = headers_list();

		if ( array_search( 'X-Redirect-By: WCSSC_Remove_Cart', $headers_list ) ) { ?>
			<div class="wcssc-msg success">
			<?php _e( 'Cart removed successfully.', 'wcssc' ); ?>
			</div><?php
		}
	}

	add_action( 'wcssc_show_message', 'wcssc_show_message' );
}

if ( ! function_exists( 'pdb' ) ) {
	/**
	 * Prints the variable for debugging purpose.
	 * Mustn't be use anywhere except development.
	 *
	 * @param $data Data to print.
	 */
	function pdb( $data ) {
		echo '<pre>';
		print_r( $data );
		echo '</pre>';
	}
}

