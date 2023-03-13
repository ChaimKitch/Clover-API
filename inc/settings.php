<?php

namespace WC_Kwac_Clover;

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

class Settings extends Base {

    public $api_base_url;
    public $auth_base_url;
    public $merchant_id;
    public $app_id;
    public $app_secret;
    public $auth_code;

    public function __construct($plugin) {
        parent::__construct($plugin);
        
        add_filter( 'woocommerce_get_sections_advanced', [ $this, 'add_advanced_section' ] );
        add_filter( 'woocommerce_get_settings_advanced', [ $this, 'add_advanced_section_fields' ], 10, 2 );
        add_action( 'woocommerce_admin_field_custom_html', [ $this, 'custom_html_field'] );

        add_action( 'admin_notices', [ $this, 'notice_incomplete' ] );

        $this->api_base_url     = get_option('wc_kwac_clover_api_base_url');
        $this->auth_base_url    = get_option('wc_kwac_clover_auth_base_url');
        $this->merchant_id      = get_option('wc_kwac_clover_merchant_id');
        $this->app_id           = get_option('wc_kwac_clover_app_id');
        $this->app_secret       = get_option('wc_kwac_clover_app_secret');

        $code = isset( $_GET['code'] ) ? $_GET['code'] : null;

        if($code){
            $this->plugin->CloverApi()->deauthorize();
            update_option('wc_kwac_clover_auth_code', $code);
        }

        $this->auth_code = get_option('wc_kwac_clover_auth_code');

    }

    public function is_complete(){
        if(!$this->plugin->Helper()->is_null_or_empty($this->api_base_url) 
        && !$this->plugin->Helper()->is_null_or_empty($this->auth_base_url) 
        && !$this->plugin->Helper()->is_null_or_empty($this->merchant_id) 
        && !$this->plugin->Helper()->is_null_or_empty($this->app_id)
        && !$this->plugin->Helper()->is_null_or_empty($this->app_secret)){
            return true;
        }
        return false;
    }

    public function is_authorize(){
        if( !$this->plugin->Helper()->is_null_or_empty($this->auth_code) ){
            return true;
        }
        return false;
    }
        
    public function custom_html_field( $value ) {
        echo '<tr valign="top">';
        echo '<th scope="row" class="titledesc">'.$value['title'].'</th>';
        echo '<td class="forminp forminp-custom">';
        echo $value['desc'];
        echo '</td>';
        echo '</tr>';
    }

    public function add_advanced_section( $sections ) {
        $sections['wc_kwac_clover_advanced_section'] = __( 'Clover', 'wc-kwac-clover' );
        return $sections;
    }

    public function show_connection_status(){
        
        $status = '';

        if($this->is_complete()){
            $status .= '<a href="'. $this->plugin->CloverApi()->get_authorize_url() .'" class="button-secondary"> Authorize app </a>';
            
            if($this->is_authorize()){

                $merchant = $this->plugin->CloverApi()->merchant();

                $status .= '<div><div class="notice notice-warning inline" style="display: inline-block;"><p style="font-size: .8em;">';
                $status .= '<span class="wp-ui-text-highlight">AUTHORIZED</span>';
                $status .= '</p></div></div>';

            }

        }

        return $status;

    }

    public function add_advanced_section_fields( $settings, $current_section ) {
        if ( $current_section == 'wc_kwac_clover_advanced_section' ) {
            
            $new_settings = array(
                array(
                    'title' => __( 'Clover API Settings', 'wc-kwac-clover' ),
                    'type' => 'title',
                    'desc' => '',
                    'id' => 'wc_kwac_clover_advanced_section_title'
                ),
                array(
                    'title' => __( 'APP ID', 'wc-kwac-clover' ),
                    'type' => 'text',
                    'desc' => __( 'Enter your APP ID', 'wc-kwac-clover' ),
                    'id' => 'wc_kwac_clover_app_id'
                ),
                array(
                    'title' => __( 'APP Secret', 'wc-kwac-clover' ),
                    'type' => 'text',
                    'desc' => __( 'Enter your APP secret', 'wc-kwac-clover' ),
                    'id' => 'wc_kwac_clover_app_secret'
                ),
                array(
                    'title' => __( 'Merchant ID', 'wc-kwac-clover' ),
                    'type' => 'text',
                    'desc' => __( 'Enter your merchant ID', 'wc-kwac-clover' ),
                    'id' => 'wc_kwac_clover_merchant_id'
                ),
                array(
                    'title' => __( 'Authorization Base URL', 'wc-kwac-clover' ),
                    'type' => 'text',
                    'desc' => __( 'Enter te Authorization (OAuth) endpoint URL', 'wc-kwac-clover' ),
                    'id' => 'wc_kwac_clover_auth_base_url'
                ),
                array(
                    'title' => __( 'API Base URL', 'wc-kwac-clover' ),
                    'type' => 'text',
                    'desc' => __( 'Enter te API base URL', 'wc-kwac-clover' ),
                    'id' => 'wc_kwac_clover_api_base_url'
                ),
                array(
                    'type' => 'custom_html',
                    'desc' => '<div class="custom-panel"> '. $this->show_connection_status() .' </div>',
                ),
                array(
                    'type' => 'sectionend',
                    'id' => 'wc_kwac_clover_advanced_section_end'
                )
            );
            return $new_settings;
        }
        return $settings;
    }

    public function notice_incomplete(){

        if(!$this->is_complete()){
            printf( '<div class="notice notice-error"><p>%s</p></div>', sprintf(__( 'Clover Settings is not complete ', 'wc-kwac-clover' )) );
        }

    }

}