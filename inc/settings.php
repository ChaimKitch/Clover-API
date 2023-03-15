<?php

namespace WC_Kwac;

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

class Settings extends Base {

    protected $prefix = 'wc_kwac_clover_';

    public $api_base_url;
    public $auth_base_url;
    public $merchant_id;
    public $app_id;
    public $app_secret;
    public $auth_code;
    public $access_token;

    public function __construct($plugin) {
        parent::__construct($plugin);
        add_action( 'admin_notices', [ $this, 'notice_incomplete' ] );
        $this->sync();
    }

    protected function key($key){
        return $this->prefix . $key;
    }

    protected function sync(){
        $this->api_base_url     = get_option($this->key('api_base_url'));
        $this->auth_base_url    = get_option($this->key('auth_base_url'));
        $this->merchant_id      = get_option($this->key('merchant_id'));
        $this->app_id           = get_option($this->key('app_id'));
        $this->app_secret       = get_option($this->key('app_secret'));
        $this->auth_code        = get_option($this->key('auth_code'));
        $this->access_token     = get_option($this->key('access_token'));
    }

    public function is_complete(){
        if(!$this->helper()->is_null_or_empty($this->api_base_url) 
        && !$this->helper()->is_null_or_empty($this->auth_base_url) 
        && !$this->helper()->is_null_or_empty($this->merchant_id) 
        && !$this->helper()->is_null_or_empty($this->app_id)
        && !$this->helper()->is_null_or_empty($this->app_secret)){
            return true;
        }
        return false;
    }

    public function set($key, $value){
        update_option($this->key($key), $value );
        $this->sync();
    }

    public function notice_incomplete(){

        if(!$this->is_complete()){
            printf( '<div class="notice notice-error"><p>%s</p></div>', sprintf(__( 'Clover Settings is not complete ', 'wc-kwac-clover' )) );
        }

    }

}