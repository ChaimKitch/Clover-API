<?php

namespace WC_Kwac_Clover;

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

class Base {

    public $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function plugin(){
        return $this->plugin;
    }

}