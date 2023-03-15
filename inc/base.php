<?php

namespace WC_Kwac;

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

class Base {

    public $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function api(){
        return $this->plugin->api;
    }

    public function settings(){
        return $this->plugin->settings;
    }

    public function helper(){
        return $this->plugin->helper;
    }

}