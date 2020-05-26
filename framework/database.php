<?php

/**
 * Cria as tabelas necessárias para ativação e utilização inicial do plugin
 * @global object $wpdb
 * @return bool True se a base foi criada com sucesso
 */
function wordyii_database_activate() {
    global $wpdb;

    return true;
}

/**
 * Remove todas as tabelas, índices e views criados para usar este plugin
 * @global object $wpdb
 * @return bool True se a base foi removida com sucesso
 */
function wordyii_database_drop() {
    global $wpdb;

}

/**
 * Verifica e atualiza caso o database esteja com a versão atualizada
 * @param integer $current_db_ver Versão atual da base de dados
 * @return bool true Se atualizado com sucesso
 */
function wordyii_database_update( $current_db_ver = null ) {
    // Compara a versão do database com a versão do plugin
    if ( ( $current_db_ver != WORDYII_DB_VER) ) {
        $error = false;
        // Verifica se existe uma função de atualização do database
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
