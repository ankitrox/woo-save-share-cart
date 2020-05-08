<?php
//prevent direct access of file.
if( !defined('ABSPATH') ) {
    exit;
}

if( !class_exists('WCSSC') ){

    class WCSSC {

        static $_instance;

        /*
         * Version of plugin
         */
        public $version = '1.0.7';

        /*
         * Plugin settings array
         */
        public $settings = array();
        
        /*
         * Options variable to hold configuration
         */
        public $configuration;

        /*
         * Table name
         */
        public $table_name;

        //constructor
        function __construct() {

			$this->includes();

            $this->settings['general']      =   include_once trailingslashit(WCSSC_BASE).'admin/lib/settings/general-settings.php';
            $this->settings['email']        =   include_once trailingslashit(WCSSC_BASE).'admin/lib/settings/email-settings.php';
            $this->settings['save_cart']    =   include_once trailingslashit(WCSSC_BASE).'admin/lib/settings/save-cart-settings.php';

            register_activation_hook( WCSSC_BASE_FILE, array( __CLASS__, 'install') );
            $this->get_settings();
            $GLOBALS['wcssc_rewrite_api'] = new WCSSC_Rewrite_API();
            register_activation_hook( WCSSC_BASE_FILE, array($this, 'install') );
            add_action( 'init', array($this, 'init') );
        }

        //Instantiate the class
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

        //Invoke necessary classes, actions etc.
        function init(){
			
			//Load plugin textdomain
			load_plugin_textdomain( 'wcssc', false, WCSSC_BASE . DIRECTORY_SEPARATOR .'languages' );
			
            $GLOBALS['wcssc_frontend'] = new WCSSC_FrontEnd();
            $GLOBALS['wcssc_saved_cart'] = new WCSSC_Saved_Cart();

			$this->settings['wcssc_user']	=	(is_user_logged_in()) ? get_current_user_id() : get_option('wcssc_user');
        }

        /*
         * Fires on activation of plugin
         */
        static function install(){

			//Create a user, who will create saved carts.
			$user = username_exists('wcssc_user');

			if( !$user ){
				$password = wp_generate_password();
				$user = wp_create_user( 'wcssc_user', $password );
				$opt = update_option( 'wcssc_user', $user, true );
			}else{
				$opt = update_option( 'wcssc_user', $user, true );
			}

			//Bind the cron on activation.
			wp_schedule_event( strtotime("now"), 'hourly', 'wcssc_clear_carts' );

            //Generate rewrite rules. Saved carts endpoint creation.
            flush_rewrite_rules();
        }

        /*
         * Include necessary files and classes
         */
        function includes(){

            include_once trailingslashit(WCSSC_BASE).'lib/functions.php';
            include_once trailingslashit(WCSSC_BASE).'admin/classes/class-wcssc-admin.php';
            include_once trailingslashit(WCSSC_BASE).'admin/classes/class-wcssc-cron.php';
            include_once trailingslashit(WCSSC_BASE).'classes/class-wcssc-post-type.php';
            include_once trailingslashit(WCSSC_BASE).'classes/class-wcssc-frontend.php';
            include_once trailingslashit(WCSSC_BASE).'classes/class-wcssc-rewrite-api.php';
            include_once trailingslashit(WCSSC_BASE).'classes/class-wcssc-ajax.php';
            include_once trailingslashit(WCSSC_BASE).'classes/class-wcssc-saved-cart.php';
        }

        //Retrieve settings for plugin in property.
        function get_settings(){

            $settings_array = $this->settings['general'] + $this->settings['email'] + $this->settings['save_cart'];

            foreach( $settings_array as $key=>$value ){
                if( isset($value['id']) ){
                    $config_value = get_option( $value['id'], null );
                    $this->configuration[$value['id']] = empty( $config_value ) ? ( empty($value['default']) ? '' : $value['default'] ) : $config_value;
                }
            }

            $this->configuration = apply_filters( 'wcssc_config_filter', $this->configuration );
        }
    }
}