<?php

namespace WC_Kwac_Clover;

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

class Helper extends Base {

    public function __construct($plugin) {
        parent::__construct($plugin);
    }

    public function is_null_or_empty($value){
        if(is_null($value) || empty($value)){
            return true;
        }else{
            if($value == ''){
                return true;
            }
        }
        return false;
    }
    
    public function log($message){
        if(is_string($message)){
            \error_log($message . PHP_EOL, 0);
        }else{
            \error_log(print_r($message, true) . PHP_EOL, 0);
        }
    }

}