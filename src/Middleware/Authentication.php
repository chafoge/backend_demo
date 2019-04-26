<?php
namespace src\Middleware;

use src\Database\Tables\Secret;
use src\Database\Tables\Table_Methods;
use src\Library\Helper;

class Authentication extends Middleware_Controller
{
    public function authenticate ($request, $response) {
        $params = $request->getParams();
        $secret_methods = new Secret($this->container);
        $table_methods = new Table_Methods($this->container);

        $required_params = ['token', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        $token_conditions = [
            'state' => 1,
            'hash' => $params['token']
        ];
        $get_token = $table_methods->get_one_secret($token_conditions);

        //check token inner 1 hour (new login, because no new token)
        $token_time_valid = $secret_methods->validate_token_time($params['token'], 1, 'hour');
        if(isset($token_time_valid['errors'])){
            $secret_methods->delete_older_token_id('token', $get_token['id'] + 1, $get_token['relation_id']);
            return $token_time_valid;
        }

        //check token belongs to uid
        if(($params['uid'] === $get_token['relation_id']) === false){
            return array('errors' => $this->container->get('state')['INVALID_UID']);
        }

        //check token inner 10 seconds
        $token_time_valid = $secret_methods->validate_token_time($params['token'], 10, 'second');
        if(isset($token_time_valid['errors'])){
            $secret_methods->delete_older_token_id('token', $get_token['id'], $get_token['relation_id']); //$get_token['id'] + 1 (was it before)
        }

        $token_params = [
            'uid' => 1,
            'relation_id' => $params['uid'],
            'relation_type' => $this->container->get('relations')['users'],
            'type' => $this->container->get('secret')['token']
        ];

        $created_token = $table_methods->create_secret($token_params);
        if(isset($created_token['errors'])){
            return $created_token;
        }

        return ["token" => $created_token['create']['hash'], "uid" => $params['uid']];
    }
}