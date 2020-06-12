<?php

namespace wordyii\behaviors;

class WordyiiAccessBehavior
{
    
    public function __construct($access, $action)
    {

        $this->access = $access;
        $this->action = $action;

        var_dump ($this->run() );
    }

    /**
     * Allowed params: allow, actions, roles, permissions.
     * 
     * in controller:
     * public function behaviors()
     * {
     *      return [
     *          'acess' => [
     *              'class' => [
     *                      'path' => '\wordyii\behaviors',
     *                      'value' => 'wordyiiaccessbehavior',
     *               ],
     *              'rules' => [
     *                  [
     *                      'allow' => true,
     *                      'roles' => ['administrator'],
     *                      'actions' => ['login'],
     *                  ],[
     *                      'allow' => false
     *                  ]
     *              ]
     *          ]
     *      ];
     * }
     */
    public function run()
    {
        $allow = false;

        $user = wp_get_current_user();

        $action = $this->action;

        // Opening 'rules' array
        foreach ($this->access['rules'] as $key => $v) {

            // if index 'allow' is set, he received
            if ( isset ($v['allow']) ) {
                $allow = $v['allow'];
            }

            // if index 'actions' is not set or the called action exist on 'actions' value
            if ( empty( $v['actions']) || in_array($action, $v['actions']) ) {
            
                // Check if exist setted roles, else return allow
                if ( ! empty ($v['roles'])) {
                    
                    // Opening 'roles' array
                    foreach ( $v['roles'] as $role) {
                        
                        // role = '@' to any logged user
                        if ( $role == '@' && is_user_logged_in() ) {
                            return $allow;
                        }
                        
                        // role = '?' to any logged out user
                        if ($role == '?' && ! is_user_logged_in()) {
                            return $allow;
                        }
                        
                        // Check if the current user role exist on 'roles'
                        if ( in_array($role, $user->roles) ) {
                            return $allow;
                        }
                    }

                } else {
                    return $allow;
                }
            }
        }
    }
}

