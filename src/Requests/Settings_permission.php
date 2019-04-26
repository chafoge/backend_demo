<?php
namespace src\Requests;

use src\Database\Tables\Table_Methods;
use src\Library\Helper;
use src\Middleware\Permission;

class Settings_permission extends Request_Controller
{
    /*
     */
    public function get_permissions($request, $response){
        $routes = $this->container->router->getRoutes();
        $table_methods = new Table_Methods($this->container);

        $methods = [];
        foreach ($routes as $route) {
            $method_name = $route->getPattern();
            $method_parts = explode('/', substr($method_name, 1));
            $last_part = count($method_parts) -1;
            $permission = $this->container->get('permission')['permission_types'];

            if(isset($permission[$method_parts[$last_part]])) {
                if(isset($methods[$method_parts[0]]) === false){
                    $methods[$method_parts[0]] = [];
                    $methods[$method_parts[0]]['methods'] = [];
                }

                if(array_search($method_parts[$last_part], $methods[$method_parts[0]]['methods']) === false){
                    $methods[$method_parts[0]]['methods'][] = $method_parts[$last_part];
                }

                $condtions['name'] = $method_parts[0];
                $condtions['state'] = 1;
                $methods[$method_parts[0]]['permissions'] = $table_methods->get_all_permissions($condtions, null, '');
            }
        }

        return $response->withJson(  $methods );
    }

    /*
     */
    public function get_permission($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['object_type', 'object'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson($errors );
        }

        $condtions = [
            'object_type' => $params['object_type'],
            'object' => $params['object']
        ];

        return $response->withJson( $table_methods->get_all_permissions($condtions) );
    }

    /*
     */
    public function create_permission($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['type', 'user_type', 'name', 'permission', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson($errors );
        }

        $get_conditions =[
            'state' => 1,
            'type' => $params['type'],
            'user_type' => $params['user_type'],
            'name' => $params['name'],
            'permission' => $params['permission'],
        ];

        if(isset($params['user_role'])){
            $get_conditions['user_role'] = $params['user_role'];
        }

        $get_permission = $table_methods->get_all_permissions($get_conditions);

        if(empty($get_permission) === false){
           foreach ($get_permission as $permission){
               $table_methods->update_state_permission(0, $params['uid'], ['id' => $permission['id']]);
           }
        }

        $created_data = Helper::is_created($table_methods->create_permission($params));
        return $response->withJson( $created_data );
    }

    /*
     */
    public function update_permission($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['permission_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $conditions['id'] = $params['permission_id'];

        $updated_data = Helper::is_updated($table_methods->update_permission($params, $conditions));
        return $response->withJson( $updated_data );
    }

    /*
     */
    public function delete_permission($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['permission_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $conditions['id'] = $params['permission_id'];

        $updated_data = Helper::is_updated($table_methods->update_state_permission(0, $params['uid'], $conditions));
        return $response->withJson( $updated_data );
    }
}