<?php
namespace src\Requests;
use src\Database\Tables\Lists;
use src\Database\Tables\Table_Methods;
use src\Database\Taxonomies\Custom_table_field;
use src\Database\Taxonomies\Taxonomie_Methods;
use src\Library\Helper;

class Global_list extends Request_Controller
{
    /* returns a multi array of requested data
     */
    public function get_list($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $lists_methods = new Lists($this->container);

        $required_params = ['list_group'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson($errors );
        }

        if($params['list_group'] === 'status_dependencys'){
            $taxonomie_methods = new Taxonomie_Methods($this->container);
            $relations = $this->container->get('relations');

            $table_field_conditions = [
                'state' => 1,
                'type' => $relations['table_field'],
                'relation_type' => $relations['users'],
                'field_type' => 'status_indicator'
            ];

            $get_table_fields = $taxonomie_methods->get_all_table_fields($table_field_conditions);

            return $response->withJson( $get_table_fields );
        }

        else{
            return $response->withJson( $table_methods->get_all_lists(
                    ['list_group' => $params['list_group']],
                    ['id', 'list_group', 'name', 'value', 'relation', 'color']
                    , ' '
                )
            );
        }
    }

    /* returns a multi array of requested data
     */
    public function get_lists($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['list_groups'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson($errors );
        }

        $lists = [];

        foreach ($params['list_groups'] as $list_key => $list){
            if($list === 'user_type'){
                array_push($lists, $this->get_all_user_types() );
            }
            else{
                $lists[$list] = $table_methods->get_all_lists(
                    ['list_group' => $list],
                    ['id', 'list_group', 'name', 'value', 'relation']
                    , ' '
                );
            }
        }

        return $response->withJson($lists );

    }

    /* returns a multi array of requested data
    */
    public function create_list ($request, $response) {
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['name', 'value', 'list_group', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson($errors );
        }

        $created_data = Helper::is_created($table_methods->create_list($params));
        return $response->withJson( $created_data);
    }

    /* returns a multi array of requested data
    */
    public function update_list ($request, $response) {
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson($errors );
        }

        $conditions = [ 'id' => $params['id'] ];
        $update_params = [ 'uid' => $params['uid'] ];

        if(isset($params['name'])){
            $update_params['name'] = $params['name'];
        }
        if(isset($params['color'])){
            $update_params['color'] = $params['color'];
        }

        $updated_data = Helper::is_updated($table_methods->update_list($update_params, $conditions));
        return $response->withJson( $updated_data);
    }
}