<?php

define( 'WORDYII', 3);
define( 'WORDYII_PATH', plugin_dir_path( __FILE__ ) );
define( 'CHILD_THEME_URI', get_stylesheet_directory_uri() );
define( 'CHILD_THEME_PATH', get_stylesheet_directory());
define( 'WORDYII_URL', plugin_dir_url( __FILE__ ) );
define( 'WORDYII_FILE', __FILE__ );

require_once(WORDYII_PATH . 'wordyii/database/database.php');
require_once(WORDYII_PATH . 'functions.php');
require_once(WORDYII_PATH . 'base/WordyiiBehavior.php');
require_once(WORDYII_PATH . 'wordyii/behaviors/WordyiiAccessBehavior.php');
require_once(WORDYII_PATH . 'base/WordyiiController.php');
require_once(WORDYII_PATH . 'base/WordyiiView.php');
// Include page templates
require_once(WORDYII_PATH . 'wordyii/views/pagetemplater.php' );

function wordyii_activate () {
    // Checks if the wordyii_db_ver attribute existes on wp_option table
    if ( !(get_option('wordyii_db_ver')) ) {
        if ( wordyii_database_activate() ) {
            // Add wordyii_db_ver atributess if not exists
            add_option('wordyii_db_ver', 1);
        }
    }
}

function wordyii_deactivate () {
    // Checks if the wordyii_db_ver attribute existes on wp_option table
    if ( (get_option('wordyii_db_ver')) ) {
        // Remove the wordyii_db_ver attribute if exists
        delete_option('wordyii_db_ver');
        wordyii_database_drop();
    }
}

function wordyii_update () {
    wordyii_database_update( get_option('wordyii_db_ver') );
}

register_activation_hook( WORDYII_FILE, 'wordyii_activate' );
register_deactivation_hook( WORDYII_FILE, 'wordyii_deactivate');
add_action( 'plugins_loaded', 'wordyii_update', 1 );

