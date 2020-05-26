<?php
/*
Plugin Name: WordYii
Version: 0.1.0
*/

define( 'WORDYII', 3);
define( 'WORDYII_PATH', plugin_dir_path( __FILE__ ) );
define( 'WORDYII_URL', plugin_dir_url( __FILE__ ) );
define( 'WORDYII_FILE', __FILE__ );

require_once(WORDYII_PATH . 'framework/database.php');
require_once(WORDYII_PATH . 'functions.php');

register_activation_hook( WORDYII_FILE, 'wordyii_activate' );
register_deactivation_hook( WORDYII_FILE, 'wordyii_deactivate');
add_action( 'plugins_loaded', 'wordyii_update', 1 );


