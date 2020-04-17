<?php
if(!defined('ABSPATH')){
    exit;
}

if( !class_exists('WCSSC_Setting_Tab') ){

    class WCSSC_Setting_Tab extends WC_Settings_Page {

        //Constructor
        function __construct() {

            $this->id       = 'wcssc';
            $this->label    = __( 'Save Share Cart', 'wcssc' );
            parent::__construct();

            add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 200 );
            add_filter( 'woocommerce_settings_tabs_wcssc', array( $this, 'settings' ) );
            add_filter( 'woocommerce_update_options_wcssc', array($this, 'update_settings') );
            add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
        }

        /*
         * Adds setting tab to woocommerce setting page
         */
        function add_settings_tab( $settings_tabs ){
            $settings_tabs['wcssc'] = __( 'Share Cart', 'wcssc' );
            return $settings_tabs;
        }

        /*
         * Add settings fields to settings tab
         */
        function settings() {

            global $current_section, $wcssc;
			
            if(in_array( $current_section, array('', 'general')) ){
                woocommerce_admin_fields( $wcssc->settings['general'] );
            }

            if( 'email' == $current_section ){
                woocommerce_admin_fields( $wcssc->settings['email'] );
            }
            
            if( 'save_cart' == $current_section ){
                woocommerce_admin_fields( $wcssc->settings['save_cart'] );
            }
        }

        /*
         * Save settings
         */
        function update_settings(){
            global $current_section, $wcssc;

            if(in_array( $current_section, array('', 'general')) ){
                woocommerce_update_options( $wcssc->settings['general'] );
            }
            
            if( 'email' == $current_section ){
                woocommerce_update_options( $wcssc->settings['email'] );
            }
            
            if( 'save_cart' == $current_section ){
                woocommerce_update_options( $wcssc->settings['save_cart'] );
            }
        }

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
                $sections['general'] = __('General', 'wcssc');
                $sections['email'] = __('Email', 'wcssc');
                $sections['save_cart'] = __('Save cart', 'wcssc');
		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Output sections.
	 */
	public function output_sections() {
		global $current_section;

		$sections        = $this->get_sections();
		$current_section = empty( $current_section ) ? 'general' : $current_section;

		if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
			return;
		}

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	}
    }

    $GLOBALS['wcssc_setting_tab'] = new WCSSC_Setting_Tab();
}