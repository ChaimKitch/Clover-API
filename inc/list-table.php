<?php

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

// Define la clase de la tabla
if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WC_CK_List_Table extends WP_List_Table {

    // Define las columnas de la tabla
    function get_columns() {
        $columns = array(
            'id' => 'ID',
            'name' => 'Nombre',
            'email' => 'Email',
            'phone' => 'Teléfono'
        );
        return $columns;
    }

    // Define los datos de la tabla
    function prepare_items() {
        $data = array(
            array( 'id' => 1, 'name' => 'Juan', 'email' => 'juan@gmail.com', 'phone' => '1234567890' ),
            array( 'id' => 2, 'name' => 'Pedro', 'email' => 'pedro@gmail.com', 'phone' => '9876543210' ),
            array( 'id' => 3, 'name' => 'María', 'email' => 'maria@gmail.com', 'phone' => '5555555555' )
        );

        // Define las columnas de la tabla
        $columns = $this->get_columns();

        // Define las columnas ocultas
        $hidden = array();

        // Define las opciones de bulk actions
        $actions = array();

        // Define los datos de la tabla
        $this->_column_headers = array( $columns, $hidden, $actions );
        $this->items = $data;
    }

    // Define el contenido de cada celda
    function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
            case 'name':
            case 'email':
            case 'phone':
                return $item[ $column_name ];
            default:
                return print_r( $item, true );
        }
    }
    
}