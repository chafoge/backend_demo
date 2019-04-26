<?php
namespace src\Database\Taxonomies;
use src\Database\Database_Controller;
use src\Library\Helper;

class Custom_field_value extends Database_Controller
{
    /*
     */
    public function create_field_value($params){
        $taxonomie_methods = new Taxonomie_Methods($this->container);
        $relations = $this->container->get('relations');

        $required_params = ['table', 'name', 'value', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        $get_table_field = $taxonomie_methods->get_one_table_field(['relation_type' => $relations[$params['table']], 'value' => $params['name']]);
        if(isset($get_table_field['id']) === false){
            return array('errors' => $this->container->get('state')['FIELD_NOT_EXISTS'], 'details' => 'table: ' . $params['table'] . ' name: ' . $params['name'] );
        }

        $field_params = [
            'type' => $relations['field'], //todo change to field_value
            'relation_type' => $relations[$params['table']],
            'relation_id' => $params['relation_id'],
            'name' => $get_table_field['value'],
            'value' => $params['value'],
            'uid' => $params['uid']
        ];

        return $taxonomie_methods->create_field($field_params);
    }

    /*
      */
    public function update_field_value($params){
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['id', 'value', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        $get_table_field = $taxonomie_methods->get_one_field(['id' => $params['id']]);
        if(isset($get_table_field['id']) === false){
            return array('errors' => $this->container->get('state')['FIELD_NOT_EXISTS']);
        }

        $update_params = [
            'value' => $params['value'],
            'uid' => $params['uid']
        ];

        $conditions = [
            'id' => $params['id'],
        ];

        return $taxonomie_methods->update_field($update_params, $conditions);
    }

    /*
     */
    public function delete_field_value($params){
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['field_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        $conditions['id'] = $params['field_id'];

        return $taxonomie_methods->update_state_field(0, $params['uid'], $conditions);
    }
}