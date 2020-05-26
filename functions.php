<?php


function wordyii_activate () {
    // Verifica se o atributo wordyii_db_ver não existe na wp_option
    if ( !(get_option('wordyii_db_ver')) ) {
        if ( wordyii_database_activate() ) {
            // Adiciona o atributo wordyii_db_ver se não existir
            add_option('wordyii_db_ver', 1);
        }
    }
}

function wordyii_deactivate () {
    // Verifica se o atributo wordyii_db_ver existe na wp_option
    if ( (get_option('wordyii_db_ver')) ) {
        // Remove o atributo wordyii_db_ver se existir
        delete_option('wordyii_db_ver');
        wordyii_database_drop();
    }
}

function wordyii_update () {
    wordyii_database_update( get_option('wordyii_db_ver') );
}

