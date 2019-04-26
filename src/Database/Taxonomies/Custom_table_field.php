<?php
namespace src\Database\Taxonomies;
use src\Database\Database_Controller;
use src\Library\Helper;

class Custom_table_field extends Database_Controller
{

    public function get_all_table_fields($table, $crm_type = null){
        $taxonomie_methods = new Taxonomie_Methods($this->container);
        $relations = $this->container->get('relations');

        $table_field_conditions = [
            'state' => 1,
            'type' => $relations['table_field'],
            'relation_type' => $relations[$table],
        ];

        if($crm_type !== null){
            $table_field_conditions['search->value'] = 'c_' . $crm_type;
        }

        $get_table_fields = $taxonomie_methods->get_all_table_fields($table_field_conditions);
        return $get_table_fields;
    }

    /*
     */
    public function create_table_field($params){
        $taxonomie_methods = new Taxonomie_Methods($this->container);
        $relations = $this->container->get('relations');

        $required_params = ['table', 'type', 'name', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        $types = [ 'text', 'number', 'url', 'password', 'email', 'textarea', 'date', 'status' ];
        if(array_search($params['type'],$types) === false){
            return array('errors' => $this->container->get('state')['UNKNOWN_FIELD_DATA_TYPE']);
        }

        $crm_type = null;
        if(isset($params['crm_type'])){
            $crm_type = $params['crm_type'];
        }

        $table_field_params = [
            'type' => $relations['table_field'],
            'relation_type' => $relations[$params['table']],
            'field_type' => $params['type'],
            'name' => $params['name'],
            'value' => $this->build_indicator($params['name'], $crm_type)
        ];

        if($params['type'] === 'status_indicator'){
            $required_params = ['dependent'];
            if(($errors = Helper::check_required_params($required_params, $params)) !== false){
                return $errors;
            }

            $table_field_params['dependent'] = $params['name'] . '$' . $params['dependent'];
        }

        $get_table_field = $taxonomie_methods->get_one_table_fields($table_field_params);
        if(empty($get_table_field) === false){
            return array('errors' => $this->container->get('state')['FIELD_ALREADY_EXISTS'], 'details' => 'table: ' . $params['table'] . ', name: ' . $params['name'] );
        }
        if($get_table_field['state'] === 0){
            return $taxonomie_methods->update_state_table_field(1, $params['uid'], ['id' => $get_table_field['id']]);
        }

        $table_field_params['uid'] = $params['uid'];
        return $taxonomie_methods->create_table_field($table_field_params);
    }

    public function build_indicator($name, $crm_type = null){
        $name = preg_replace ( '/[^a-z0-9 ]/i', '', $name);
        $name = preg_replace ( '/\s+/', '_', $name);

        if($crm_type === null) {
            $name = 'c__' . strtolower($name);
        }
        else {
            $crm_type = preg_replace ( '/[^,;a-zA-Z0-9_-]|[,;]$/s', '', $crm_type);
            $crm_type = preg_replace ( '/\s+/', '_', $crm_type);
            $name = 'c_' . strtolower($crm_type) .'__' . strtolower($name);
        }

        return $name;
    }


    /*
     */
    public function update_table_field($params){
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['id', 'name', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        $conditions = [
            'id' => $params['id'],
        ];

        $field_params = [
            'uid' => $params['uid'],
            'name' => $params['name']
        ];

        return $taxonomie_methods->update_table_field($field_params, $conditions);
    }

    /*
     */
    public function delete_table_field($params){
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        $conditions['id'] = $params['id'];

        return $taxonomie_methods->update_state_table_field(0, $params['uid'], $conditions);
    }
}