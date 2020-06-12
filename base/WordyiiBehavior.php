<?php

namespace wordyii\base;

class WordyiiBehavior
{
    public $params;

    /**
     * Create the object class called by controller function behaviors
     */
    public function __construct($params, $action = null)
    {

        $this->params = $params;

        foreach ($this->params as $behaviorClass => $value)  {

            // Set the default namespace
            $namespace = 'wordyii\behaviors\\';

            // Set the namespace if was inserted in function behaviors
            if ( ! empty ($value['class']['value'] && $value['class']['path'] )) {
                $namespace = $value['class']['path'];
                $behaviorClass = $value['class']['value'];
            }

            // Check if exists the behavior class file
            if ( $this->findBehaviorFile($behaviorClass, $namespace) ) {

                // Receive the name class with namespace
                $class = $namespace . $behaviorClass;

                // Starts the class passing your parameters
                $obj = new $class( $value, $action );
            }
        }
    }

    /**
     * Check if the params passed on behaviors exists
     * @return bool true if exists
     */
    public function findBehaviorFile($param, $namespace)
    {

        try {

            $path = WORDYII_PATH . $namespace . $param . '.php';

            if ( file_exists($path) ) {
                return true;
            }

            throw new \Exception("Behavior class does not exist");

        } catch (\Exception $e) {
            echo $e;
        }
        
        return false;
    }
}

