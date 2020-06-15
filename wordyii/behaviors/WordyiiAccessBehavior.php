<?php

namespace wordyii\behaviors;

class WordyiiAccessBehavior
{
    
    public function __construct($access, $action)
    {
        $this->access = $access;
        $this->action = $action;
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
        $user = wp_get_current_user();

        $calledAction = $this->action;

        // if doesn't have defined rule, default return false
        if ( empty($this->access['rules']) ) {
            return false;
        }
        
        // Return the permission checking all the conditions rules
        foreach($this->access['rules'] as $key => $v) {

            // Define the return permission
            if ( isset ($v['allow']) ) {
                $allow = $v['allow'];
            } else {
                $allow = false;
            }

            // Check all conditions
            // Check if the called action exists on condition rules
            if ( empty ($v['actions']) || in_array($calledAction, $v['actions']) ) {

                // If any role in the rule matches the user conditions, return the permission
                if (! empty ($v['roles']) ) {
                    
                    foreach ($v['roles'] as $role) {

                        // role = '@' to any logged user
                        if ($role == '@' && is_user_logged_in() ) {
                            return $allow;
                        }

                        // role = '?' to any logged out user
                        if ($role == '?' && ! is_user_logged_in() ) {
                            return $allow;
                        }

                        // Check if the current user role exist on 'roles'
                        if ( in_array($role, $user->roles) ) {
                            return $allow;
                        }
                    }
                }

                // Validate access by user permissions if any given
    			if ( ! empty( $v['permissions'] ) ) {
					foreach ($v['permissions'] as $permission) {
						if ( current_user_can( $permission ) ) {
							return $allow;
						}
					}
                }

                // If the user set one action, but not set a role or permission, return the permission (The rule will be valued for all roles)
                if ( empty ($v['roles']) && empty($v['permissions']) ) {
                    return $allow;
                }
            }
            // Otherwise, continue to the next array
        }

        // Se nenhum dos parâmetros se equivalerem, retorna falso
        // If no parameters were found, return false
        return false;
    }
}

