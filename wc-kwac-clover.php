<?php

/**
 * Plugin Name: KWAC Clover for Wordpress
 * Plugin URI: https://kwac.media/
 * Description: Clover custom extension for WooCommerce by KWAC.
 * Version: 1.0.0
 * Author: KWAC MEDIA
 * Author URI: https://kwac.media/
 * Text Domain: wc-kwac-clover
 * Domain Path: /languages
 *
 * @package WC_Kwac_Clover
 */

namespace WC_Kwac_Clover;

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

require_once( 'inc/base.php' );
require_once( 'inc/helper.php' );
require_once( 'inc/settings.php' );
require_once( 'inc/clover-api.php' );

/**
 * Main plugin class.
 *
 * @package WC_Custom_Extension
 */
class WC_Kwac_Clover {

    /**
     * The minimum PHP version required for the plugin.
     */
    const MIN_PHP_VERSION = '7.0.0';

    /**
     * The minimum WordPress version required for the plugin.
     */
    const MIN_WP_VERSION = '5.0.0';

    	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var WC_Kwac_Clover The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return WC_Kwac_Clover An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

    /**
     * Instance of the Helper class.
     *
     * @var WC_Kwac_Clover\Helper
     */
    public $helper;

    /**
     * Instance of the Settings class.
     *
     * @var WC_Kwac_Clover\Settings
     */
    public $settings;

    /**
     * Instance of the Settings class.
     *
     * @var WC_Kwac_Clover\Clover_Api
     */
    public $clover_api;

    protected $requirements_errors;

    /**
     * Constructor.
     */
    public function __construct() {

        register_activation_hook( __FILE__, [$this, 'activation'] );

        if( $this->check_requirements() ){

            add_action( 'init', [ $this, 'initialize_plugin' ] );

            $this->helper = new Helper( $this );
            $this->settings = new Settings( $this );
            $this->clover_api = new CloverApi( $this );

        }else{

            add_action( 'admin_notices', [ $this, 'show_requirements' ] );

        }

    }

    /**
     * Check plugin requirements.
     */
    public function check_requirements() {
        
        $errors = [];

        if ( ! class_exists( 'WooCommerce' ) ) {
            $errors[] = sprintf(
                __( 'KWAC Clover for Wordpress requires WooCommerce plugin to be installed and activated.', 'wc-kwac-clover' ),
                self::MIN_PHP_VERSION,
                PHP_VERSION
            );
        }

        // Check PHP version.
        if ( version_compare( PHP_VERSION, self::MIN_PHP_VERSION, '<' ) ) {
            $errors[] = sprintf(
                __( 'KWAC Clover for Wordpress requires PHP version %s or higher. Your server is running PHP version %s.', 'wc-kwac-clover' ),
                self::MIN_PHP_VERSION,
                PHP_VERSION
            );
        }

        // Check WordPress version.
        if ( version_compare( get_bloginfo( 'version' ), self::MIN_WP_VERSION, '<' ) ) {
            $errors[] = sprintf(
                __( 'KWAC Clover for Wordpress requires WordPress version %s or higher. You are currently running WordPress version %s.', 'wc-kwac-clover' ),
                self::MIN_WP_VERSION,
                get_bloginfo( 'version' )
            );
        }

        $this->requirements_errors = $errors;

        if(!empty($errors)){
            return false;
        }

        return true;
        
    }

    public function show_requirements(){
        if ( ! empty( $this->requirements_errors ) ) {
            printf( '<div class="notice notice-error"><p>%s</p></div>', implode( '<br>', $this->requirements_errors ) );
        }
    }

    public function activation(){
        add_action( 'admin_notices', [ $this, 'show_activation' ] );
    }

    public function show_activation(){
        if ( ! empty( $this->requirements_errors ) ) {
            printf( '<div class="notice notice-error"><p>%s</p></div>', __( 'KWAC Clover for Wordpress has ben activated successfully.', 'wc-kwac-clover' ) );
        }
    }

    public function initialize_plugin(){
        
    }

    public function CloverApi(){
        return $this->clover_api;
    }

    public function Settings(){
        return $this->settings;
    }

    public function Helper(){
        return $this->helper;
    }

}

/*function my_extension_init(){
    //$WC_KWAC_CLOVER = WC_Kwac_Clover::instance();
}

add_action( 'plugins_loaded', 'my_extension_init' );
*/


