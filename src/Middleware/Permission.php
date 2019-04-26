<?php
namespace src\Middleware;

use src\Database\Tables\Table_Methods;
use src\Library\Helper;

class Permission extends Middleware_Controller
{
    public function permission($request, $response){
        $route = $request->getAttribute('route');
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        $user = $table_methods->get_one_user(['id' => $params['uid']]);
        $method_parts = explode('/', substr($route->getPattern(), 1));
        $method_name = $method_parts[count($method_parts) -1];
        $lists = $this->container->get('lists');
        $notes = $lists['notifications'];

        $condtions = [
            'state' => 1,
            'name' => $method_parts[0],
            'user_type' => $user['type'],
            'user_role' => $user['role']
        ];

        $permissions = $table_methods->get_all_permissions($condtions, null, '');

        if(empty($permissions)){
            unset($condtions['user_role']);
            $permissions = $table_methods->get_all_permissions($condtions, null, '');

            //check exists
            if(empty($permissions)){
                if($user['type'] > $lists['user_types']['admin']){
                    return array(
                        'perm' => ['errors' => $notes['PERMISSION_DENIED']],
                        'type' => $user['type'],
                        'role' => $user['role']
                        );
                }
                else {
                    return [
                        'perm' => $lists['permission_states']['admin_write'],
                        'type' => $user['type'],
                        'role' => $user['role']
                    ];
                }
            }
        }

        foreach ($permissions as $permission){
            //check spezial
            if($permission['permission'] === 'true'){
                continue;
            }
            if($permission['permission'] === 'false'){
                return array(
                    'perm' => ['errors' => $notes['PERMISSION_DENIED']],
                    'type' => $user['type'],
                    'role' => $user['role']
                );
            }

            //check standard
            if( isset($lists['permission_states'][$method_name]) &&
                $lists['permission_states'][$method_name] <= $permission['permission']){
                continue;
            }
            else {
                return array(
                    'perm' => ['errors' => $notes['PERMISSION_DENIED']],
                    'type' => $user['type'],
                    'role' => $user['role']
                );
            }
        }

        return [
            'perm' => $permission['permission'],
            'type' => $user['type'],
            'role' => $user['role'],
        ];
    }
}