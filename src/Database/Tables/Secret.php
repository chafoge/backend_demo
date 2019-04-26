<?php
namespace src\Database\Tables;
use src\Database\Database;
use src\Database\Database_Controller;
use src\Database\Methods;
use src\Library\Helper;

class Secret extends Database_Controller

{
    protected $table = 'secret';

    public function create_secret($columns){
        $methods = new Methods($this->container);
        $columns['hash'] = $this->fill_hash($columns);
        if(isset($columns['hash']['errors'])){
            return $columns['hash'];
        }
        return $methods->create($this->table, $columns);
    }
    public function update_secret($columns, $conditions = null){
        $methods = new Methods($this->container);
        $columns['hash'] = $this->fill_hash($columns);
        if(isset($columns['hash']['errors'])){
            return $columns['hash'];
        }
        return $methods->update($this->table, $columns, $conditions);
    }
    public function update_state($state, $user_id, $conditions = null){
        $methods = new Methods($this->container);
        return $methods->update_state($this->table, $state, $user_id, $conditions);
    }



    public function fill_hash($columns){
        if($columns['type'] == $this->container->get('secret')['password']){
           return password_hash($columns['hash'], PASSWORD_DEFAULT);
        }

        else if(
            $columns['type'] == $this->container->get('secret')['token'] ||
            $columns['type'] == $this->container->get('secret')['validation']
        ){
            return Helper::generateRandomString(50);
        }
        
        else {
            return array('errors' => $this->container->get('state')['UNKNOWN_SECRET_TYPE']);
        }
    }



    public function disable_older_token_id($token_typ, $token_id, $user_id){

        $conditions = [
            'smaller->id' => $token_id,
            'relation_id' => $user_id,
            'relation_type' => $this->container->get('relations')['users'],
            'type' => $this->container->get('secret')[$token_typ]
        ];

        return $this->update_state(0, 1, $conditions);
    }



    public function delete_older_token_id($token_typ, $token_id, $user_id){
        $database_methods = new Database($this->container);

        $conditions = [
            'smaller->id' => $token_id,
            'relation_id' => $user_id,
            'relation_type' => $this->container->get('relations')['users'],
            'type' => $this->container->get('secret')[$token_typ]
        ];

        $query = "DELETE FROM secret";
        $where = [];
        foreach ($conditions as $key => $value){
            $where[] = $database_methods->build_where_clause_item($key, $value);
        }
        $query .= " WHERE " . print_r(implode(' AND ', $where), true);
        $statement = $this->container['db']->prepare($query);
        return $statement->execute();
    }



    public function validate_token_time($token, $time_since, $time_unit){
        $table_methods = new Table_Methods($this->container);

        //get token
        $token_conditions = [
            'state' => 1,
            'hash' => $token
        ];
        $get_token = $table_methods->get_one_secret($token_conditions);

        //check exists
        if(isset($get_token['id']) === false) {
            return array('errors' => $this->container->get('state')['INVALID_TOKEN']);
        }

        //check expired
        $time_since_token_created = Helper::time_unit_amount(Helper::time_ago($get_token['create_date']), $time_unit);
        if($time_since_token_created > $time_since){
            return array('errors' => $this->container->get('state')['EXPIRED_TOKEN'], 'details' => $time_since_token_created);
        }

        return true;
    }
}