<?php

namespace WC_Kwac;

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

class Actions extends Base{

    public function __construct($plugin) {

        parent::__construct($plugin);
        add_action( 'init', [ $this, 'initialize' ] );

    }

    public function initialize(){
        
        add_rewrite_rule(
            '^wckwacclover/([^/]*)/?',
            'index.php?wc_kwac_clover_action=$matches[1]',
            'top'
        );

        add_filter( 'query_vars', [ $this, 'query_vars' ] );
        add_action( 'parse_request', [ $this, 'parse_request' ] );

    }

    public function query_vars($vars){
        $vars[] = 'wc_kwac_clover_action';
        return $vars;
    }

    public function process_clover_callback(){
        
        $code = isset( $_GET['code'] ) ? $_GET['code'] : null;

        if($code){
    
            $this->settings()->set('access_token', null);
            $this->settings()->set('auth_code', $code);

        }

        wp_redirect(admin_url('admin.php?page=wc-settings&tab=advanced&section=wc_kwac_clover_advanced_section'));
        die();

    }

    public function parse_request( $wp ){
        
        $action = (isset($wp->query_vars['wc_kwac_clover_action'])) ? $wp->query_vars['wc_kwac_clover_action'] : '';
        
        switch($action) {
            case 'clover_oauth_callback':
                $this->process_clover_callback();
                break;        
        }

    }
   
    
}
