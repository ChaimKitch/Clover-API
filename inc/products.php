<?php

namespace WC_Kwac;

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

require_once 'list-table.php';

class Products extends Base {

    public function __construct($plugin) {
        parent::__construct($plugin);
        add_action('admin_menu', [$this, 'register_submenu_page']);
    }

    public function register_submenu_page() {
        add_submenu_page( 'edit.php?post_type=product', 'Clover products', 'Clover products', 'manage_options', 'clover-submenu-page', [$this, 'clover_submenu_page_callback'] ); 
    }
    
    public function clover_submenu_page_callback() {
        ?>

        <div class="wrap">
        <h1 class="wp-heading-inline">Clover Products</h1>
        <a href="#" class="page-title-action">Button</a>
        <hr class="wp-header-end">
        </div>

        <?php
        $table = new \WC_CK_List_Table();
        $table->prepare_items();
        ?>
        <?php $table->display(); ?>
        <?php


    }

    public function list_table() {

    }

}
