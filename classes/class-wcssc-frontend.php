<?php
if(!defined('ABSPATH')){
    exit;
}

if( !class_exists('WCSSC_FrontEnd') ){

    class WCSSC_FrontEnd {

        function __construct() {

			$button_pos = apply_filters( 'wcssc_button_pos', array(
				'woocommerce_before_cart_table',
				'woocommerce_after_cart_table',
				'woocommerce_before_cart',
				'woocommerce_after_cart',
                'woocommerce_cart_actions'
			));
			
			foreach( (array)$button_pos as $pos){
				add_action( $pos, array($this, 'render_share_button') );
			}
			
            add_action( 'wp_enqueue_scripts', array($this, 'scripts_styles') );
            add_action( 'wp_footer', array( $this, 'wcssc_dialog' ) );
            add_action( 'woocommerce_account_menu_items', array($this, 'add_nav_item') );
			add_filter( 'the_content', array($this, 'single_saved_cart_content'), 99 );
			add_filter( 'the_title', [ $this, 'saved_carts_title' ], 10, 2 );
        }

	    /**
	     * Enqueue scripts and styles.
	     */
	    function scripts_styles() {

		    if ( apply_filters( 'wcssc_load_scripts', false ) ) {
			    $wcssc_vars = array( 'loader' => $this->loader_markup(), 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
			    wp_register_script( 'wcssc-frontend-js', trailingslashit( WCSSC_BASE_URL ) . 'assets/js/wcssc-frontend.js', array(
				    'jquery',
				    'jquery-ui-dialog'
			    ), false, true );
			    wp_localize_script( 'wcssc-frontend-js', 'wcssc_vars', $wcssc_vars );
			    wp_enqueue_script( 'wcssc-frontend-js' );
			    wp_enqueue_style( 'wp-jquery-ui-dialog' );
			    wp_enqueue_style( 'wcssc-font-awesome', 'https://use.fontawesome.com/releases/v5.12.1/css/all.css', array(), false );
			    wp_enqueue_style( 'wcssc-front-css', trailingslashit( WCSSC_BASE_URL ) . 'assets/css/wcssc-style.css', array(), false );
		    }
	    }

        /*
         * Display Share Cart button
         */
        function render_share_button(){

            global $wcssc;

            if( current_action() == $wcssc->configuration['wc_wcssc_button_pos'] ){?>
                <button class="button button-primary" id="wcssc-share-cart"><?php echo $wcssc->configuration['wcssc_btn_txt']; ?></button><?php
            }
        }

        /*
         * Ajax loader markup
         */
        function loader_markup( $echo = false ){

            $markup = '<p style="text-align: center;"><img src="'.  trailingslashit(WCSSC_BASE_URL).'assets/img/ajax-loader.gif" /></p>';

            if( $echo )
                echo $markup;
            else
                return $markup;
        }
        
        /*
         * Append dialog box in page at footer
         */
        function wcssc_dialog(){?>
            <div id="wcssc-share-box" style="display: none;">
                <?php $this->loader_markup(true); ?>
            </div><?php
        }

        /*
         * Add navigation item to my account page.
         */
        function add_nav_item( $items ){
            $items['saved-carts'] = __( 'Saved carts', 'wcssc' );
            return $items;
        }

		/**
		 * Include template for single saved cart.
		 * You can overwrite this template by using it in your theme
		 */
	    function single_saved_cart_content( $content ) {
	        $filtered_content = '';
		    if ( is_singular( 'wcssc-cart' ) ) {

			    global $post;
			    $cart = get_post_meta( $post->ID, 'wcssc_cart_data' );

			    if ( ! empty( $cart ) ) {

				    $cart = $cart[0];

				    $template = locate_template( 'single-saved-cart.php', false, false );

				    if ( empty( $template ) ) {
					    $template = WCSSC_BASE . '/templates/single-saved-cart.php';
				    }

				    ob_start();
				    require $template;
				    $filtered_content = ob_get_clean();
			    }
		    }

		    return ( $filtered_content ) ? $filtered_content : $content;
	    }

	    /**
	     * Saved cart posts' title.
	     *
	     * @param string Post's title.
	     * @param $id Post ID.
         *
         * @return string Filtered title.
	     */
		public function saved_carts_title( $title, $id ) {
			global $wcssc;

			$post_data      = get_post( $id );
			$has_post_title = get_post_meta( $id, 'wcssc_has_title', true );

		    if( ( $post_data->post_type === 'wcssc-cart' ) && empty( $has_post_title ) ){
			    $title = $wcssc->configuration['wcssc_shared_cart_page_title'];
            }

		    return $title;
        }
    }
}