<?php

namespace wordyii\base;

class View
{

    /**
     * $fileView View path
     */
    public $fileView;

    public function __construct($calledClass = null)
    {
        $this->calledClass = $calledClass;
    }

    /**
     * Renders a view
     */
    public function render($viewName, $model = null)
    {
        $this->fileView = $this->findViewFile($viewName);

        if ( ! empty($this->fileView) ) {
            require $this->fileView;
        }
    }


    /**
     * @param string View name file
     * @return string Return string if view exists, otherwise return false.
     */
    public function findViewFile($view)
    {
        try {

            // Remove controller from the object
            $teste = str_replace('Controller', '', $this->calledClass);
            $rest = explode("\\", $teste);
            // Take the last result from array
            $controllerName = end($rest);
            $controllerName = strtolower($controllerName);
            
            $path = WORDYII_PATH . 'wordyii/views/' . $controllerName . '/' . $view . '.php';

            if ( file_exists( $path ) ) {
                return $path;
            }
            
            throw new \Exception("View file not founded");
        
        } catch (\Exception $e) {
            echo $e;
        }

        return false;
    }

}