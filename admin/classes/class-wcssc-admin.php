<?php
if(!defined('ABSPATH')){
    exit;
}

if( !class_exists('WCSSC_Admin') ){

    class WCSSC_Admin {
		
		public $cron_handler;

		/*
         * Constructor
         */
        function __construct() {
			
			$this->cron_handler = new WCSSC_Cron;
            add_filter( 'woocommerce_get_settings_pages', array($this, 'setting_page') );
            add_filter( 'woocommerce_settings_saved', array($this, 'settings_saved') );
        }

        /*
         * Include necessary files.
         */
        function setting_page( $settings ){
            $settings[] = include_once trailingslashit(WCSSC_BASE).'admin/classes/class-wcssc-setting-tab.php';
            return $settings;
        }
		
		/*
		 * 
		 */
		function settings_saved(){

			global $wcssc;

			if( !empty($_REQUEST['page']) && !empty($_REQUEST['tab']) && $_REQUEST['page'] == 'wc-settings' && $_REQUEST['tab'] === 'wcssc' ) {

				if( !empty($_POST['wcssc_ip_interval']) && is_int(intval($_POST['wcssc_ip_interval'])) ){

					/*
					 * Clear the schedule everytime on save and if data is present, add it again.
					 */
					wp_clear_scheduled_hook( 'wcssc_clear_carts' );

					$wcssc->configuration['wcssc_ip_interval'] = intval( $_POST['wcssc_ip_interval'] );

					do_action( 'wcssc_settings_saved', $_POST['wcssc_ip_interval'] );
				}
			}
		}
    }

	function wcssc_admin(){
		return new WCSSC_Admin();
	}
	add_action( 'plugins_loaded', 'wcssc_admin' );
}