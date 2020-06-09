<?php

namespace wordyii\behaviors;

class WordyiiAccessBehavior
{
    /**
     * Allowed params: allow, actions, roles, permissions.
     * 
     * in controller:
     * public function behaviors()
     * {
     *      return [
     *          'acess' => [
     *              'allow' => true,
     *              'roles' => ['admin'],
     *              'actions' => ['index'],
     *          ]
     *      ];
     * }
     */
    public function __construct($params)
    {

        foreach ($params as $attribute => $value) {

            // Set default allow true
            $allow = true;

            if ($value === true ) {
                return true;
            }

            if ($attribute == 'roles' && is_array($value) ) {
                
            }

            if ($attribute == 'actions' && is_array($value)) {
                $action = $value;
            }



        }

        var_dump (wp_get_current_user()->roles);
        // var_dump (current_user_can());
    }
}