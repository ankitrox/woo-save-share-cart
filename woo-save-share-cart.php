<?php
/**
 * Plugin Name: Save and Share Cart for WooCommerce
 * Plugin URI: http://sharethingz.com/
 * Description: A WooCommerce plugin to share cart on different social media platforms.
 * Version: 1.0.8
 * Author: Ankit Gade
 * Author URI: https://sharethingz.com/
 * Text Domain: wcssc
 * Domain Path: /i18n/languages/
 *
 * @package WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( !defined( 'WCSSC_BASE' ) ){
    define( 'WCSSC_BASE', dirname(__FILE__) );
}

if( !defined( 'WCSSC_BASE_FILE' ) ){
    define( 'WCSSC_BASE_FILE', __FILE__ );
}

if( !defined( 'WCSSC_BASE_URL' ) ){
    define( 'WCSSC_BASE_URL', plugins_url( basename( dirname(__FILE__) ) ) );
}

//Include main class
if( !class_exists('WCSSC') ) {
    include_once dirname( __FILE__ ) . '/classes/class-wcssc.php';
}

function WCSSC(){
    return WCSSC::instance();
}

$GLOBALS['wcssc'] = WCSSC();
