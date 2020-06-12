<?php

namespace wordyii\base;

abstract class WordyiiModel {

    /**
    * Create an object on database
    * @return bool True if sucess saved
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
     * Update an object on database
     * @return bool true if sucess updated
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
     * Remove object from the database
     * @return bool True if sucess removed
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
     * Validate the attributes of the executed class
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
     * Return array with add values and attributes
     * @return array $arr
     */
    public function attributes() {
        // Recebe todas as variáveis da classe executada
        $vars = get_class_vars( get_called_class() );
        $arr = [];

        // Cycles through the variables and inserts into the array
        foreach ($vars as $idx => $v) {
            $arr = array_merge( $arr, array( $idx => $this->{$idx} ) );
        }

        return $arr;
    }

    /**
     * Return table name
     * @return string
     */
    public static function tableName() {

        return '';
    }
    
    /**
     * Searches all columns from the database and returns a object list.
     * @param array $conditions Search conditions. Default NULL
     * @param array $orderBy Search ordering. Default NULL
     * @param integer $page Page index from the object list. Default 0
     * @param integer $pageSize Maxibum number of objects to return. Default -1 (no pagination)
     * @return array Class object list.
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
     * Searches all columns from the database and returns a object list.
     * @param array $conditions Search conditions. Default NULL
     * @param array $orderBy Search ordering. Default NULL
     * @param integer $page Page index from the object list. Default 0
     * @param integer $pageSize Maxibum number of objects to return. Default -1 (no pagination)
     * @return integer Amount of results found
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
    * Return a unique object pulling from the database
    * @param array $condition Search conditions
    * @return Object Object of the requested class. NULL if not found
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
     * Convert an object to the called class
     * @param array Unique object in list
     * @return object Return a object from the called class
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
     * @return string $query Returns query with the prepared attribute condition
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

                    // Inserts the attribute and string LIKE no $arr
                    array_push($arr, $v[1] . " LIKE %s");
                    // Inserts the value to be searched
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

        // Separate query string by AND
        $queryAtts = implode(' AND ', $arr);

        $query .= " WHERE " . $queryAtts;

        $query = $wpdb->prepare($query, $values);

        return $query;
    }

}

