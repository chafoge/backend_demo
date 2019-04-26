<?php
namespace src\Database\Tables;
use src\Database\Database_Controller;
use src\Database\Methods;
use src\Library\Helper;

class Contact extends Database_Controller
{
    protected $table = 'contact';

    public function create_contact($columns){
        $methods = new Methods($this->container);
        if($errors = $this->pre_validate_contact($columns)['errors']){
            return $errors;
        }
        else {
            return $methods->create($this->table, $columns);
        }
    }
    public function update_contact($columns, $conditions = null){
        $methods = new Methods($this->container);
        if($errors = $this->pre_validate_contact($columns)['errors']){
            return $errors;
        }
        else {
            return $methods->update($this->table, $columns, $conditions);
        }
    }
    public function update_state_contact($state, $user_id, $conditions = null){
        $methods = new Methods($this->container);
        return $methods->update_state($this->table, $state, $user_id, $conditions);
    }



    public function pre_validate_contact($params){
        if($params['name'] === 'phone'){
            if(($error = Helper::validate($params['value'],'integer')) !== $this->container->get('state')['VALID']){
                return array('errors' => array('phone' => array('error' =>$error)));
            }
        }

        if($params['name'] === 'email'){
            if(($error = Helper::validate($params['value'],'email')) !== $this->container->get('state')['VALID']){
                return array('errors' => array('email' => array('error' =>$error)));
            }
        }
    }
}