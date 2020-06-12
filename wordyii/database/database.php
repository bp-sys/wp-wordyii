<?php

/**
 * Create the necessary tables for activation and initial use of the plugin
 * @global object $wpdb
 * @return bool True if the base was successfully created
 */
function wordyii_database_activate() {
    global $wpdb;

    return true;
}

/**
 * Remove all the table, indexes and views created to use the plugin
 * @global object $wpdb
 * @return bool True if the base was successfully removed
 */
function wordyii_database_drop() {
    global $wpdb;

}

/**
 * Checks and update if database version is older
 * @param integer $current_db_ver Current database version
 * @return bool true Se atualizado com sucesso
 */
function wordyii_database_update( $current_db_ver = null ) {
    // Compares the database version with the plugin version
    if ( ( $current_db_ver != WORDYII_DB_VER) ) {
        $error = false;
        // Checks if exist a update database function
        for ( $i = $current_db_ver + 1 ; function_exists ('wordyii_database_update_' . $i) ; $i++ ) {
            // Chama a função de atualização
            $callFunc = 'wordyii_database_update_' . $i;
            $return = $callFunc();

            if (!$return) {
                $error = true;
            }
        }

        if (!$error){
            return true;
        }
    }

    return false;
}

