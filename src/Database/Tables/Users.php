<?php
namespace src\Database\Tables;

use src\Database\Database_Controller;
use src\Database\Taxonomies\Taxonomie_Methods;
use src\Library\Helper;
use src\Mail\Mail;
use src\Mail\Templates\Templates;

class Users extends Database_Controller
{
    public function signup_user( $request ){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['uid', 'type', 'email'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        if($params['type'] === $this->container->get('users_type')['user']){
            $params['role'] = 1;
            $user_params = ['firstname', 'lastname'];
            if(($errors = Helper::check_required_params($user_params, $params)) !== false){
                return $errors;
            }
        }

        else if($params['type'] === $this->container->get('users_type')['company']){
            $user_params = ['name'];
            if(($errors = Helper::check_required_params($user_params, $params)) !== false){
                return $errors;
            }
        }

        else if($params['type'] === $this->container->get('users_type')['admin']){
            return $this->container->get('state')['PERMISSION_DENIED'];
        }

        //check user email exists
        $get_email_params = [
            'name' => 'email',
            'value' => $params['email']
        ];
        $get_email = $table_methods->get_one_contact($get_email_params);
        if(isset($get_email['id'])){
            return ['errors' => $this->container->get('state')['EMAIL_ALREADY_EXISTS'], 'details' => $get_email['value']];
        }

        //create user
        $created_user = $table_methods->create_user($params);
        if(isset($created_user['errors'])){
            return $created_user;
        }

        //create gender
        $gender_params['value'] = 2;
        $gender_params['relation_id'] = $created_user['create']['id'];
        $gender_params['relation_type'] = $this->container->get('relations')['users'];
        $gender_params['uid'] = $params['uid'];

        $created_gender = $taxonomie_methods->create_gender($gender_params);
        if(isset($created_gender['errors'])){
            return $created_gender;
        }


        //create email
        $email_params['name'] = 'email';
        $email_params['value'] = $params['email'];
        $email_params['relation_id'] = $created_user['create']['id'];
        $email_params['relation_type'] = $this->container->get('relations')['users'];
        $email_params['uid'] = $params['uid'];

        $created_email = $table_methods->create_contact($email_params);
        if(isset($created_email['errors'])){
            return $created_email;
        };

        //create verfification token
        $token_params['type'] = $this->container->get('secret')['validation'];
        $token_params['relation_id'] = $created_user['create']['id'];
        $token_params['relation_type'] = $this->container->get('relations')['users'];
        $token_params['uid'] = $params['uid'];

        $created_token = $table_methods->create_secret($token_params);
        if(isset($created_token['errors'])){
            return $created_token;
        }

        //send verification mail
        $mail_class = new Mail($this->container);
        $template_class = new Templates($this->container);
        $mail = [];

        $mail['head']['from']['adress'] = 'noreply@groe.me';
        $mail['head']['from']['name'] = 'groe-app';
        $mail['head']['to']['adress'] = $created_email['create']['value'];
        $mail['head']['to']['name'] = $created_user['create']['firstname'] . ' ' . $created_user['create']['lastname'];
        $mail['head']['subject'] = 'groe - Email Verifizierung';
        $mail['head']['message'] = $template_class->user_email_validation(
            $created_user['create'],
            $created_email['create']['value'],
            $created_token['create']['hash']);

        $sended_mail = $mail_class->send($mail);
        if(isset($created_token['errors'])){
            return $sended_mail;
        }

        return array('create' => true);
    }


    public function get_user_type($user_id){
        $table_methods = new Table_Methods($this->container);
        return $table_methods->get_one_user(
            ['id' => $user_id],
            ['type']
        );
    }


    public function setback_password ($request) {
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $taxonomie_methods = new Taxonomie_Methods($this->container);
        $lists = $this->container->get('lists');

        $required_params = ['email'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $errors;
        }

        //check user email exists
        $get_email_params = [
            'name' => 'email',
            'value' => $params['email']
        ];
        $get_email = $table_methods->get_one_contact($get_email_params);
        if(isset($get_email['id']) === false){
            return ['errors' => $lists['notifications']['UNKNOWN_USER']];
        }

        //get user
        $get_user = $table_methods->get_one_user([ 'id' => $get_email['relation_id']]);
        if(isset($get_user['id'])  === false){
            return ['errors' => $lists['notifications']['UNKNOWN_USER']];
        }

        //create verfification token
        $token_params['type'] = $lists['secret_types']['validation'];
        $token_params['relation_id'] = $get_user['id'];
        $token_params['relation_type'] = $lists['relations']['users'];
        $token_params['uid'] = 0;

        $created_token = $table_methods->create_secret($token_params);
        if(isset($created_token['errors'])){
            return $created_token;
        }

        //send verification mail
        $mail_class = new Mail($this->container);
        $template_class = new Templates($this->container);
        $mail = [];


        $mail['head']['from']['adress'] = 'noreply@groe.me';
        $mail['head']['from']['name'] = 'groe-app';
        $mail['head']['to']['adress'] = $get_email['value'];
        $mail['head']['to']['name'] = $get_user['firstname'] . ' ' . $get_user['lastname'];
        $mail['head']['subject'] = 'groe - Email Verifizierung';
        $mail['head']['message'] = $template_class->user_email_validation(
            $get_user,
            $get_email['value'],
            $created_token['create']['hash']);

        $sended_mail = $mail_class->send($mail);
        if(isset($created_token['errors'])){
            return $sended_mail;
        }

        return array('update' => true);
    }
}