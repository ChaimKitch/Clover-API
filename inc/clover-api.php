<?php

namespace WC_Kwac_Clover;

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

class CloverApi extends Base{

    
    protected $access_token;

    protected $merchant;


    public function __construct($plugin) {
        parent::__construct($plugin);
        $this->access_token = get_option('wc_kwac_clover_access_token');
    }

    public function call( $method, $url, $data = [], $headers = [] ) {
        
        if($this->plugin->Settings()->is_complete()){

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

        }else{
            return new \WP_Error( 'clover_settings_incomplete', __( 'Clover settings incomplete', 'wc-kwac-clover' ) );
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

    public function deauthorize(){
        $this->access_token = null;
        $this->plugin->Settings()->auth_code = null;
        update_option('wc_kwac_clover_access_token', $this->access_token );
        update_option('wc_kwac_clover_auth_code', null );
    }

    public function get_authorize_url(){

        $redirect_url = "http" . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "s" : "") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $url = $this->plugin->Settings()->auth_base_url . 'oauth/authorize?' . http_build_query([
            'client_id'     => $this->plugin->Settings()->app_id,
            'redirect_uri'  => $redirect_url
        ]);

        return $url;

    }

    protected function request_access_token(){

        $this->access_token = null;

        $this->plugin->Helper()->log("REQUEST ACCESS TOKEN");
        
        $url = $this->plugin->Settings()->auth_base_url . 'oauth/token';

        $this->plugin->Helper()->log($url);
        
        $data = [
            'client_id'     => $this->plugin->Settings()->app_id,
            'client_secret' => $this->plugin->Settings()->app_secret,
            'code'          => $this->plugin->Settings()->auth_code
        ];

        $this->plugin->Helper()->log($data);

        $response = $this->call('GET', $url, $data);

        if(!is_null($response) && !is_wp_error($response) ){
            $this->access_token = isset($response->access_token) ? $response->access_token : null;
            if($this->access_token){
                update_option('wc_kwac_clover_access_token', $this->access_token);
            }
            $this->plugin->Helper()->log($this->access_token);
            return $this->access_token;
        }else{
            $this->plugin->Helper()->log("REQUEST ACCESS ERROR");
            $this->deauthorize();
            $this->plugin->Helper()->log($response);
            return false;
        }

    }

    public function get_access_token(){
        if(!$this->access_token){
            $this->request_access_token();
        }
        return $this->access_token;
    }

    public function merchant(){
        $this->plugin->Helper()->log("MERCHANT");
        if($this->get_access_token()){
            $url = $url = $this->plugin->Settings()->api_base_url . 'merchants/' . $this->plugin->Settings()->merchant_id;
            $data = [
                'accept' => 'application/json',
                'authorization' => 'Bearer ' . $this->access_token
            ];
            $this->plugin->Helper()->log("MERCHANT RESPONSE");
            $this->plugin->Helper()->log($data);
            $merchant_response = $this->call('GET', $url, null, $data);
            $this->plugin->Helper()->log($merchant_response);
        }
        return false;
    }

   
    
}
