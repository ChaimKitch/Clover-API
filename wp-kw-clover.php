<?php
/**
 * Plugin Name: KWAC Clover Wordpress plugin
 * Description: Integration with Clover.
 * Plugin URI:  https://kwac.media
 * Version:     1.0.0
 * Author:      KWAC MEDIA
 * Author URI:  https://kwac.media
 * Text Domain: wp-kw-clover-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_KW_Clover_Plugin {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '5.6';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var WP_KW_Clover_Plugin The single instance of the class.
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
	 * @return WP_KW_Clover_Plugin An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	public function __construct() {

        add_action( 'init', [ $this, 'initialize_plugin' ] );

	}

	public function initialize_plugin(){
        // Add actions here 
        add_action( 'admin_menu', [$this, 'kw_add_settings_page'] );
        add_action( 'admin_init', [$this, 'kw_register_settings'] );
        
	}

    public function kw_add_settings_page(){
        add_options_page( 'Clover Settings', 'Clover Settings', 'manage_options', 'kw_clover_settings', [$this, 'kw_clover_render_plugin_settings_page'] );
    }

    public function kw_clover_render_plugin_settings_page(){
        if ( !current_user_can( "manage_options" ) )  {
            wp_die( __( "You do not have sufficient permissions to access this page." ) );
        }
    
        if ( isset($_GET['status']) && $_GET['status']=='success') { 
        ?>
            <div id="message" class="updated notice is-dismissible">
                <p><?php _e("Settings updated!", "wp-kw-clover"); ?></p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text"><?php _e("Dismiss this notice.", "wp-kw-clover"); ?></span>
                </button>
            </div>
        <?php
        }
        ?>
        <form action="<?php echo admin_url( 'options.php'); ?>" method="post">
        
            <?php 
            
                settings_fields( 'kw_clover_plugin_options' );
                do_settings_sections( 'kw_clover_plugin' ); 
                submit_button();
            ?>          
            
        </form>
        <?php
    }

    public function kw_register_settings() {

        register_setting( 'kw_clover_plugin_options', 'kw_clover_plugin_options');
        add_settings_section( 'kw_clover_plugin_setting',  __( 'Clover Settings', 'kw-clover' ), [$this,'kw_clover_plugin_section_text'], 'kw_clover_plugin' );

        add_settings_field( 'kw_clover_pluging_setting_id_merchant', __( 'ID Merchant', 'kw-clover' ), [$this, 'kw_clover_pluging_setting_id_merchant'], 'kw_clover_plugin', 'kw_clover_plugin_setting' );
        add_settings_field( 'kw_clover_pluging_setting_api_token', __( 'API Token', 'kw-clover' ), [$this, 'kw_clover_pluging_setting_api_token'], 'kw_clover_plugin', 'kw_clover_plugin_setting' );
        add_settings_field( 'kw_clover_pluging_setting_api_secret', __( 'API Secret', 'kw-clover' ), [$this, 'kw_clover_pluging_setting_api_secret'], 'kw_clover_plugin', 'kw_clover_plugin_setting' );
        add_settings_field( 'kw_clover_pluging_setting_endpoint_url', __( 'Endpoint URL', 'kw-clover' ), [$this, 'kw_clover_pluging_setting_endpoint_url'], 'kw_clover_plugin', 'kw_clover_plugin_setting' );

    
    }

    function kw_clover_plugin_options_validate( $input ) {
        $url = trim(rtrim($input['url'],"/"));
        if(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)) {
            $input['url'] = $url;
            return $input;
        }
        return false;
    }


    function kw_clover_plugin_section_text() {
        echo '<p>Here you can set all the options for connect Clover API</p>';
    }
    
    function kw_clover_plugin_setting_url() {
        $options = get_option( 'kw_clover_plugin_options' );
        $url = (isset($options['url'])) ? $options['url'] : '';
        echo "<input id='kw_clover_plugin_options_url' name='kw_clover_plugin_options[url]' type='text' value='" . esc_attr( $url ) . "' />";
    }
    function kw_clover_pluging_setting_id_merchant(){
        ?> <input type="text" id="kw_clover_plugin_options_id_merchant" name="kw_clover_plugin_options[id_merchant]" value="<?php echo get_option( "kw_clover_plugin_options" )['id_merchant'] ?? '' ?>"> <?php
    }
    function kw_clover_pluging_setting_api_token(){
        ?> <input type="text" id="kw_clover_plugin_options_api_token" name="kw_clover_plugin_options[api_token]" value="<?php echo get_option( "kw_clover_plugin_options" )['api_token'] ?? '' ?>"> <?php
    }
    function kw_clover_pluging_setting_api_secret(){
        ?> <input type="text" id="kw_clover_plugin_options_api_secret" name="kw_clover_plugin_options[api_secret]" value="<?php echo get_option( "kw_clover_plugin_options" )['api_secret'] ?? '' ?>"> <?php
    }
    function kw_clover_pluging_setting_endpoint_url(){
        ?> <input type="text" id="kw_clover_plugin_options_endpoint_url" name="kw_clover_plugin_options[endpoint_url]" value="<?php echo get_option("kw_clover_plugin_options" )['endpoint_url'] ?? '' ?>"> <?php
    }

    function kw_clover_plugin_setting_entrypoint_page() {
        $options = get_option( 'kw_clover_plugin_options' );
        $entrypoint_page = (isset($options['entrypoint_page'])) ? $options['entrypoint_page'] : null;
        
        $pages = get_posts([
            'numberposts'   => -1,
            'post_type'     => 'page'
        ]);

        echo '<select id="kw_clover_plugin_options_page" name="kw_clover_plugin_options[entrypoint_page]">';
        
        foreach($pages as $page){
            $selected = ($page->ID == $entrypoint_page) ? 'selected="selected"' : "";
            echo '<option value="'. $page->ID .'" '.$selected.'>'. esc_html( $page->post_title ) .'</option>';
        }
        
        echo '</select>';

    }

}

$WP_KW_Clover_Plugin = WP_KW_Clover_Plugin::instance();