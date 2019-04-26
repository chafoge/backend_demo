<?php
namespace src\Requests;
use src\Database\Tables\Secret;
use src\Database\Tables\Table_Methods;
use src\Database\Tables\Users;
use src\Library\Helper;

class Authenticate extends Request_Controller
{
    public function signup($request, $response){
        $users_methods = new Users($this->container);
        return $response->withJson($this->render(  $users_methods->signup_user( $request ),$request ));
    }

    public function forgot($request, $response){
        $users_methods = new Users($this->container);
        return $response->withJson($this->render(  $users_methods->setback_password( $request ),$request ));
    }



    public function reset_password($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $secret_methods = new Secret($this->container);

        $required_params = ['reset_token', 'password'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson($this->render( $errors,$request ));
        }

        //get token
        $reset_token_conditions = [
            'state' => 1,
            'hash' => $params['reset_token']
        ];
        $get_reset_token = $table_methods->get_one_secret($reset_token_conditions);


        //check exists
        if(isset($get_reset_token['id']) === false){
            return $response->withJson($this->render(
                array('errors' => $this->container->get('state')['INVALID_TOKEN']),$request
            ));
        }

        //check expired
        $token_validation = $secret_methods->validate_token_time($get_reset_token['id'], 5, 'day');
        if(isset($created_token['errors'])){
            $secret_methods->disable_older_token_id('validation',$get_reset_token['id'], $get_reset_token['relation_id']);
            return $response->withJson( $this->render( $token_validation,$request ));
        }

        //create _password
        $password_params = [
            'hash' => $params['password'],
            'uid' => 1,
            'relation_id' => $get_reset_token['relation_id'],
            'relation_type' => $this->container->get('relations')['users'],
            'type' => $this->container->get('secret')['password']
        ];

        $created_password = $table_methods->create_secret($password_params);
        if(isset($created_password['errors'])){
            return $response->withJson( $this->render( $created_password,$request ));
        }

        $secret_methods->disable_older_token_id('validation',intval($get_reset_token['id']) + 1, $get_reset_token['relation_id']);
        $secret_methods->disable_older_token_id('password',intval($created_password['create']['id']), $get_reset_token['relation_id']);
        return $response->withJson($this->render( array('create' => true),$request ));
    }



    public function login($request, $response, $next){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $secret_methods = new Secret($this->container);

        $required_params = ['email', 'password'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson($this->render( $errors,$request ));
        }

        //get user by email, check exists
        $email_conditions = [
            'state' => 1,
            'name' => 'email',
            'value' => $params['email']
        ];
        $get_email = $table_methods->get_one_contact( $email_conditions, null, 'ORDER BY id DESC');
        if(isset($get_email['id']) === false){
            return $response->withJson( $this->render( array('errors' => $this->container->get('state')['UNKNOWN_EMAIL']),$request ));
        }

        //get passwords, check exists
        $password_conditions = [
            'state' => 1,
            'type' => $this->container->get('secret')['password'],
            'relation_type' => $this->container->get('relations')['users'],
            'relation_id' => $get_email['relation_id']
        ];
        $get_password = $table_methods->get_one_secret( $password_conditions, null, 'ORDER BY id DESC');
        if(isset($get_password['id']) === false){
            return $response->withJson( $this->render( array('errors' => $this->container->get('state')['UNREGISTERED_USER']),$request ));
        }

        //check password valid
        if(password_verify($params['password'], $get_password['hash'])){
            //generate_token
            $token_params = [
                'uid' => 1,
                'relation_id' => $get_email['relation_id'],
                'relation_type' => $this->container->get('relations')['users'],
                'type' => $this->container->get('secret')['token']
            ];

            $created_token = $table_methods->create_secret($token_params);
            if(isset($created_token['errors'])){
                return $response->withJson( $this->render( $created_token,$request));
            }

            //delete all older token
            $secret_methods->delete_older_token_id('token', $created_token['create']['id'], $get_email['relation_id']);
            return $response->withJson( $this->render( array("token" => $created_token['create']['hash'], "uid" => $get_email['relation_id']),$request ));
        }
        else {
            return $response->withJson( $this->render( array('errors' => $this->container->get('state')['INVALID_PASSWORD']),$request ));
        }
    }




    public function render ($response_data, $request) {
        $params = $request->getParams();
        $response['response'] = $response_data;

        if ( isset($response_data['uid'])) {
            $response['uid'] = $response_data['uid'];
        }

        if ( isset($response_data['token'])) {
            $response['token'] = $response_data['token'];
        }

        $return_token = isset($params['request_token']) ? $params['request_token'] : null;
        if ( $return_token != null) {
            $response['request_token'] = $return_token;
        }

        return $response;
    }
}