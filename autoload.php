<?php

if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

spl_autoload_register( function( $class ) {
    $prefix = 'WC_Kwac_Clover\\';
    $base_dir = __DIR__ . '/inc/';

    // Comprobar si la clase utiliza el prefijo de namespace. 
    $len = strlen( $prefix );
    if ( strncmp( $prefix, $class, $len ) !== 0 ) {
        // Si la clase no utiliza el prefijo de namespace, no la cargamos.
        return;
    }

    // Obtener el nombre de la clase relativo al namespace. 
    $relative_class = substr( $class, $len );

    // Reemplazar el separador de namespace con el separador de directorio. 
    $file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

    // Si el archivo existe, lo cargamos. 
    if ( file_exists( $file ) ) {
        require $file;
    }

} );