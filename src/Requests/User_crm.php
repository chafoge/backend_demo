<?php
namespace src\Requests;
use src\Database\Tables\Table_Methods;
use src\Database\Tables\Users;
use src\Database\Taxonomies\Crm;
use src\Database\Taxonomies\Custom_field_value;
use src\Database\Taxonomies\Custom_table_field;
use src\Library\Helper;


class User_crm extends Request_Controller
{
    /* returns a multi array of requested data
     */
    public function get_user_crm($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $crm_methods = new Crm($this->container);
        $ctf_methods = new Custom_table_field($this->container);
        $lists = $this->container->get('lists');

        $required_params = ['crm_type'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        $crms = [
            'user' => [
                'users' => [
                    'table' => 'users',
                    'indicator' => true,
                    'conditions' =>[
                        'type' => $lists['user_types']['user'],
                        'state' => 1
                    ],
                    'columns' => ['id', 'lastname', 'firstname', 'role', 'state']
                ]
            ],
            'admin' => [
                'users' => [
                    'table' => 'users',
                    'indicator' => true,
                    'conditions' =>[
                        'type' => $lists['user_types']['admin'],
                        'state' => 1
                    ],
                    'columns' => ['id', 'lastname', 'firstname', 'role', 'state']
                ]
            ],
            'company' => [
                'users' => [
                    'table' => 'users',
                    'indicator' => true,
                    'conditions' =>[
                        'type' => $lists['user_types']['company'],
                        'state' => 1
                    ],
                    'columns' => ['id', 'name', 'role', 'state']
                ],
            ]
        ];


        $crm_type = $params['crm_type'];
        if(($pos = strpos($params['crm_type'], '_')) !== false){
            $crm_type =  substr( $params['crm_type'], 0, $pos);
            $user_role = substr( $params['crm_type'], $pos +1);

            $crms[$crm_type]['users']['conditions']['role'] = $lists['user_roles'][$crm_type][$user_role];
        }

//        return $response->withJson($crms[$crm_type]);

        $tables = $crms[$crm_type];

        $custom_fields = $crm_methods->build_custom_field('users', ['id', 'value'], $params['crm_type']);
        foreach ($custom_fields as $custom_field_key =>  $custom_field){
            $tables[$custom_field_key] = $custom_field;
        }

        $required_indeicator_columns = [];

        $count_users = count($table_methods->get_all_users(
            $crms[$crm_type]['users']['conditions'],
            ['id'],
            ' '
        ));

        return $response->withJson([
            'count' => $count_users,
            'rows' => $crm_methods->get_crm($tables, $params, $required_indeicator_columns),
            'fields' => $ctf_methods->get_all_table_fields('users', $params['crm_type'])
        ]);
    }



    //field values  ///////////////////////////////////////////////////////////////

    /* Builds up the basic database dependencies
         */
    public function create_field_value($request, $response){
        $params = $request->getParams();
        $cfv_methods = new Custom_field_value($this->container);

        return $response->withJson( $cfv_methods->create_field_value($params) );
    }

    /* Builds up the basic database dependencies
     */
    public function update_field_value($request, $response){
        $params = $request->getParams();
        $cfv_methods = new Custom_field_value($this->container);

        return $response->withJson( $cfv_methods->update_field_value($params) );
    }

    /* Builds up the basic database dependencies
     */



    //table fields  ///////////////////////////////////////////////////////////////

    /* Builds up the basic database dependencies
     */
    public function create_table_field($request, $response){
        $params = $request->getParams();
        $ctf_methods = new Custom_table_field($this->container);

        return $response->withJson( $ctf_methods->create_table_field($params) );
    }

    /* Builds up the basic database dependencies
     */
    public function update_table_field($request, $response){
        $params = $request->getParams();
        $ctf_methods = new Custom_table_field($this->container);

        return $response->withJson( $ctf_methods->update_table_field($params) );
    }

    /* Builds up the basic database dependencies
     */
    public function delete_table_field($request, $response){
        $params = $request->getParams();
        $ctf_methods = new Custom_table_field($this->container);

        return $response->withJson( $ctf_methods->delete_table_field($params) );
    }



    //table fields  ///////////////////////////////////////////////////////////////

    /* creates and returns true or false
     */
    public function create_user($request, $response){
        $users_methods = new Users($this->container);
        return $response->withJson(  $users_methods->signup_user( $request ) );
    }

    /* updates and returns true or false
     */
    public function update_user($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $updated_data = Helper::is_updated($table_methods->update_user($params, [ 'id' => $params['user_id' ]]));
        return $response->withJson( $updated_data );
    }

    /* updates and returns true or false
     */
    public function delete_user($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $conditions['id'] = $params['user_id'];

        $updated_data = Helper::is_updated( $table_methods->update_state_user(0, $params['uid'], $conditions) );
        return $response->withJson( $updated_data );
    }
}
