<?php

namespace WC_Kwac;

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

class Api extends Base{

    protected $merchant;

    public function __construct($plugin) {
        parent::__construct($plugin);
        if($this->is_authorize()){
            if(!$this->settings()->access_token){
                $this->request_access_token();
            }
        }
    }

    public function call( $method, $url, $data = [], $headers = [] ) {
        
        $args = array(
            'headers' => $headers
        );
        
        if ( ! empty( $data ) ) {
            $args['body'] = $data;
        }
    
        switch ( $method ) {
            case 'GET':
                $response = wp_remote_get( $url, $args );
                break;
            case 'POST':
                $response = wp_remote_post( $url, $args );
                break;
            default:
                return new \WP_Error( 'invalid_method', __( 'Invalid HTTP method', 'wc-kwac-clover' ) );
        }

        
        if ( !is_wp_error( $response ) ) {

            $status_code = wp_remote_retrieve_response_code( $response );
            $status_message = wp_remote_retrieve_response_message( $response );
        
            if ( $status_code < 200 || $status_code >= 300 ) {
                return new \WP_Error( 'http_error', $status_message, array(
                    'status_code' => $status_code,
                    'response' => $response
                ) );
            }
        
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body );
        
            return $data;
            
        }

        return null;
    
    }

    public function is_authorize(){
        if( !$this->helper()->is_null_or_empty($this->settings()->auth_code) ){
            return true;
        }
        return false;
    }

    public function get_authorize_url(){

        //$redirect_url = "http" . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "s" : "") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        $redirect_url = site_url() . "/wckwacclover?wc_kwac_clover_action=clover_oauth_callback";

        $url = $this->settings()->auth_base_url . 'oauth/authorize?' . http_build_query([
            'client_id'     => $this->settings()->app_id,
            'redirect_uri'  => $redirect_url,
            'merchant_id'   => $this->settings()->merchant_id
        ]);

        return $url;

    }

    protected function request_access_token(){

        $this->helper()->log("REQUEST ACCESS TOKEN");
        
        $url = $this->settings()->auth_base_url . 'oauth/token';

        $this->helper()->log($url);
        
        $data = [
            'client_id'     => $this->settings()->app_id,
            'client_secret' => $this->settings()->app_secret,
            'code'          => $this->settings()->auth_code
        ];

        $this->helper()->log($data);

        $response = $this->call('GET', $url, $data);

        if(!is_null($response) && !is_wp_error($response) ){
            $access_token = isset($response->access_token) ? $response->access_token : null;
            if($access_token){
                $this->settings()->set('access_token',$access_token);
            }
            $this->helper()->log($access_token);
            return $access_token;
        }else{
            $this->helper()->log("REQUEST ACCESS ERROR");

            $this->settings()->set('access_token',null);
            $this->settings()->set('auth_code',null);

            $this->helper()->log($response);
            return false;
        }

    }

    public function merchant(){
        $this->helper()->log("MERCHANT");
        $this->helper()->log($this->settings());

        if( !$this->helper()->is_null_or_empty($this->settings()->access_token) ){

            $url = $url = $this->settings()->api_base_url . 'merchants/' . $this->settings()->merchant_id;
            
            $data = [
                'accept' => 'application/json',
                'authorization' => 'Bearer ' . $this->settings()->access_token
            ];

            $this->helper()->log("MERCHANT RESPONSE");
            $this->helper()->log($data);
            
            $merchant_response = $this->call('GET', $url, null, $data);
            $this->helper()->log($merchant_response);
            
            if(!is_wp_error($merchant_response)){
                return $merchant_response;
            }

        }

        return false;
    }

   
    
}
