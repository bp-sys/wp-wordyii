<?php

namespace wordyii\models;

abstract class BaseModel {

    /**
    * Cria o objeto na base de dados
    * @return bool True se salvo com sucesso.
    */
    public function save() {
        global $wpdb;

        if ( $this->validate() ) {

            
            if ( empty($this->id) ) {
                
                $wpdb->insert( static::tableName(), $this->attributes() );
            
                if ($wpdb->insert_id !== false) {
                    $this->id = $wpdb->insert_id;

                    return true;
                } 
            }   
            else {
                $this->update();
            }
        }

        return false;
    }
    
    /**
     * @global object $wpdb
     * Atualiza no banco de dados objeto já existente
     * @return bool true Se atualizado com sucesso
     */
    public function update() {

        global $wpdb;

        $result = $wpdb->get_row( "SELECT * FROM ".static::tableName(). " WHERE ID = " . $this->id );

        if ($result) {

            $wpdb->update(static::tableName(), $this->attributes(), array(
                'id' => $this->id
            ));

            return true;
        }

        return false;
    }

    /**
     * Remove o objeto da base de dados
     * @return bool True se removido com sucesso.
     */
    public function delete() {
        global $wpdb;

        $result = $wpdb->delete (static::tableName(), $this->attributes());

        if ($result) {
            
            return $result;
        }

        return false;
    }

    /**
     * Faz a validação dos atributos da classe executada
     * @return bool true
     */
    public function validate() {
        $vars = get_class_vars( get_called_class() );

        foreach ($vars as $idx => $v) {
            if ( empty($this->{$idx}) ) {
                $this->{$idx} = '';
            }
        }

        return true;
    }

    /**
     * Retorna array com atributos e valores adicionados
     * @return array $arr
     */
    public function attributes() {
        // Recebe todas as variáveis da classe executada
        $vars = get_class_vars( get_called_class() );
        $arr = [];

        // Percorre as variáveis e insere no array
        foreach ($vars as $idx => $v) {
            $arr = array_merge( $arr, array( $idx => $this->{$idx} ) );
        }

        return $arr;
    }

    /**
     * Retorna o nome da tabela
     * @return string
     */
    public static function tableName() {

        return '';
    }
    
    /**
     * Busca todas as colunas da base de dados e retorna como uma lista de objetos.
     * @param array $conditions Condicoes da busca. Default NULL
     * @param array $orderBy Ordenação da busca. Default NULL
     * @param integer $page Indice do "página" da lista de objetos a ser retornado. Default 0
     * @param integer $pageSize Quantidade maxima de objetos para retornar. Default -1 (sem paginação)
     * @return array Lista com os objetos da classe
     */
    public static function find($conditions = null, $orderBy = null, $page = null, $pageSize = null) {
        global $wpdb;

        $arr = [];
        $values = [];
        $ordenerArray = [];
        
        $query = "SELECT * FROM " . static::tableName();

        if (! empty( $conditions ) ) {

            $query = static::prepareConditions($query, $conditions);
        }

        if (! empty ( $orderBy )) {

            $order = '';

            foreach ($orderBy as $idx => $v){
                switch ($v) {
                    case SORT_DESC:
                        $orderValue = 'DESC';
                        break;
                    case SORT_ASC:
                        $orderValue = 'ASC';
                        break;
                }
                
                $order .= $idx . " " . $orderValue . " ";
            }
            
            if(! empty($order) ){
                $query .= " ORDER BY " . $order;
            }
        }

        if ($pageSize !== null) {
            $query .= " LIMIT " . $pageSize;

            if (! empty ($page) ) {
        
                $startLine = $pageSize * ($page - 1);

                $query .= " OFFSET " . $startLine;
            }
        }

        $results = $wpdb->get_results( $query );

        foreach ($results as $idx => $v) {
            $results[$idx] = static::cast($v);
        }

        return $results;
    }

    /**
     * Busca todas as colunas da base de dados e retorna como uma lista de objetos.
     * @param array $conditions Condicoes da busca. Default NULL
     * @param array $orderBy Ordenação da busca. Default NULL
     * @param integer $page Indice do "página" da lista de objetos a ser retornado. Default 0
     * @param integer $pageSize Quantidade maxima de objetos para retornar. Default -1 (sem paginação)
     * @return array Lista com os objetos da classe
     */
    public static function count($conditions = null, $orderBy = null, $page = null, $pageSize = null) {
        global $wpdb;

        $arr = [];
        $values = [];
        $ordenerArray = [];
        
        $query = "SELECT COUNT(*) FROM " . static::tableName();

        if (! empty( $conditions ) ) {

            $query = static::prepareConditions($query, $conditions);
        }

        if (! empty ( $orderBy )) {

            $order = '';

            foreach ($orderBy as $idx => $v){
                switch ($v) {
                    case SORT_DESC:
                        $orderValue = 'DESC';
                        break;
                    case SORT_ASC:
                        $orderValue = 'ASC';
                        break;
                }
                
                $order .= $idx . " " . $orderValue . " ";
            }

            if(! empty($order) ){
                $query .= " ORDER BY " . $order;
            }
        }

        if ($pageSize !== null) {
            $query .= " LIMIT " . $pageSize;

            if (! empty ($page) ) {
        
                $startLine = ($pageSize * $page) - 1;

                $query .= " OFFSET " . $startLine;
            }
        }

        $results = $wpdb->get_var( $query );

        return $results;
    }

    /**
    * @global Object $wpdb
    * Retorna um unico objeto puxando da base de dados
    * @param array $condition condicoesda busca
    * @return Object Objeto da classe requisitada ou NULL se nao encontrado.
    */
    public static function findOne ($condition) {
        global $wpdb;
        
        $query = "SELECT * FROM " . static::tableName();
        $query = static::prepareConditions($query, $condition);

        $result = $wpdb->get_row($query);
        
        if ( !empty ($result) ) {
            $result = static::cast($result);

            return $result;
        }

        return false;
    }


    /**
     * Converte um objeto para a classse chamada
     * @param array Objeto único na lista
     * @return object Retorna objeto da classe chamada 
     */
    public static function cast($stdClassObject) {
        
        $class = get_called_class();
        $object = new $class();

        foreach (get_object_vars ($stdClassObject) as $property => $v) {
            $object->$property = $v;
        }
       
        return $object;

    }

    /**
     * @param string $query
     * @param array $conditions
     * @return string $query Retorna query com condições de atributos preparadas
     */
    private static function prepareConditions($query, $conditions) {
        global $wpdb;
        
        $arr = [];
        $values = [];
        $operator = '=';
        $like = '';

        foreach($conditions as $idx => $v) {

            if ( is_array($conditions[$idx]) ) {

                // ['like', 'name', 'search_text']
                if ($v[$idx] == 'like') {

                    // Insere atributo e string LIKE no $arr
                    array_push($arr, $v[1] . " LIKE %s");
                    // Insere o valor a ser procurado
                    array_push($values, "%" . $wpdb->esc_like($v[2]) . "%");
                    
                    continue;
                } 
                
                // ['=', 'id', 'value']
                else if ($v[$idx] == '=') {
                    
                    $operator =     $v[0];
                    $attribute =    $v[1];
                    $value =        $v[2];
    
                    array_push($arr, $attribute . ' ' . $operator . ' ' . $value );
                    continue;
                }
                
                else if ($v[$idx] == '<=') {
                    
                    $operator =     $v[0];
                    $attribute =    $v[1];
                    $value =        $v[2];
    
                    array_push($arr, $attribute . ' ' . $operator . ' ' . $value );
                    continue;
                }

                else if ($v[$idx] == '>=') {
                    
                    $operator =     $v[0];
                    $attribute =    $v[1];
                    $value =        $v[2];
    
                    array_push($arr, $attribute . ' ' . $operator . ' ' . $value );
                    continue;
                }

                else if ($v[$idx] == '<>') {
                    
                    $operator =     $v[0];
                    $attribute =    $v[1];
                    $value =        $v[2];
    
                    array_push($arr, $attribute . ' ' . $operator . ' ' . $value );
                    continue;
                }
            } 
                
            array_push( $arr, $idx . " = %s" );
            array_push( $values, $v );
        }

        // Separa string da query por AND
        $queryAtts = implode(' AND ', $arr);

        $query .= " WHERE " . $queryAtts;

        $query = $wpdb->prepare($query, $values);

        return $query;
    }

}


