<?php

namespace wordyii\base;

use wordyii\base\View;
use wordyii\base\Behavior;

class Controller
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
        new Behavior( $this->behaviors() );

        $this->beforeAction();
        $this->runAction();
        $this->afterAction();
    }

    /**
     * 
     */
    public function behaviors()
    {
        return [];
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

        // var_dump($wp->request);
        // var_dump($wp->query_vars);

        try{
            
            $actionName = filter_input( INPUT_GET, 'action' );

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
        $view = new View( get_called_class() );
        $view->render($viewName);
    }

}


