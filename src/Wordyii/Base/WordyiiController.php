<?php

namespace Wordyii\Base;

use wordyii\base\WordyiiView;
use wordyii\base\WordyiiBehavior;

class WordyiiController
{
    /**
     * Run the controller functions
     * First run behaviors
     * Second run beforeAction
     * Thirdy run runAction
     * Fourth run afterAction
     */
    public function run()
    {
        // Receive the action passed by URL
        $this->action = filter_input( INPUT_GET, 'action' );

        // Create the Behavior class
        new WordyiiBehavior( $this->behaviors(), $this->action );

        $this->beforeAction();
        $this->runAction();
        $this->afterAction();
    }

    /**
     * 
     */
    public function behaviors()
    {
        return [
            
        ];
    }

    /**
     * 
     */
    public function viewAction()
    {

    }

    /**
     * 
     */
    public function beforeAction()
    {
        $this->behaviors();
    }

    /**
     * Check if the action passed on URL exists
     * @return string The action method
     */
    public function runAction()
    {

        global $wp;

        try{
            
            $actionName = $this->action;

            $method = 'action' . $actionName;
            $class = get_called_class();
            $object = new $class();

            if ( method_exists($object, $method) ) {
                return $object->$method();
            }

            throw new \Exception("The action does not exist");

        } catch (\Exception $e) {
            echo $e;
        }
        
        return null;
    }

    /**
     * 
     */
    public function afterAction()
    {

    }

    /**
     * Crete a view object and call render method
     */
    public function render($viewName)
    {
        $view = new WordyiiView( get_called_class() );
        $view->render($viewName);
    }

}


