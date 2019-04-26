<?php
namespace src\Requests;
use src\Database\Tables\Table_Methods;
use src\Database\Tables\Users;
use src\Database\Taxonomies\Custom_field_value;
use src\Database\Taxonomies\Taxonomie_Methods;
use src\Library\Helper;
use src\Requests\Request_Controller;
use src\Uploads\Uploads;

class User_admin extends Request_Controller
{
    /* returns a multi array of requested data
     */
    public function get_user($request, $response)
    {
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $taxonomie_methods = new Taxonomie_Methods($this->container);


        $required_params = ['user_id'];
        if (($errors = Helper::check_required_params($required_params, $params)) !== false) {
            return $response->withJson($errors);
        }

        $user['user'] = $table_methods->get_one_user(
            ['id' => $params['user_id']],
            ['id', 'lastname', 'firstname', 'name', 'image', 'gender', 'description']
        );
        if (isset($user['user']['id']) === false) {
            return $response->withJson(array('errors' => $this->container->get('state')['UNKNOWN_USER']));
        }

//        $description = $taxonomie_methods->get_one_description(
//            [
//                'relation_id' => $user['user']['id'],
//                'relation_type' => $this->container->get('relations')['users']
//            ],
//            ['value']
//        );
//        $user['user']['description'] = isset($description['value'])
//            ? $description['value']
//            : null;

//        $gender = $taxonomie_methods->get_one_gender(
//            [
//                'relation_id' => $user['user']['id'],
//                'relation_type' => $this->container->get('relations')['users']
//            ],
//            ['value']
//        );
//        $user['user']['gender'] = isset($gender['value'])
//            ? $gender['value']
//            : null;

        $user['adress'] = $table_methods->get_one_adress(
            [
                'relation_id' => $user['user']['id'],
                'relation_type' => $this->container->get('relations')['users'],
                'type' => 'shipping',
                'state' => 1
            ]
        );
        $user['adress']['shipping'] = $user['adress'] !== false
            ? $user['adress']
            : null;

        $contact_conditions = [
            'relation_id' => $user['user']['id'],
            'relation_type' => $this->container->get('relations')['users'],
            'state' => 1
        ];

        $contact_conditions['name'] = 'phone';
        $user['contact'][$contact_conditions['name']] = $table_methods->get_one_contact($contact_conditions);
        $user['contact'][$contact_conditions['name']] = $user['contact'][$contact_conditions['name']] !== false
            ? $user['contact'][$contact_conditions['name']]
            : null;

        $contact_conditions['name'] = 'email';
        $user['contact'][$contact_conditions['name']] = $table_methods->get_one_contact($contact_conditions);
        $user['contact'][$contact_conditions['name']] = $user['contact'][$contact_conditions['name']] !== false
            ? $user['contact'][$contact_conditions['name']]
            : null;

        $contact_conditions['name'] = 'skype';
        $user['contact'][$contact_conditions['name']] = $table_methods->get_one_contact($contact_conditions);
        $user['contact'][$contact_conditions['name']] = $user['contact'][$contact_conditions['name']] !== false
            ? $user['contact'][$contact_conditions['name']]
            : null;

        $contact_conditions['name'] = 'discord';
        $user['contact'][$contact_conditions['name']] = $table_methods->get_one_contact($contact_conditions);
        $user['contact'][$contact_conditions['name']] = $user['contact'][$contact_conditions['name']] !== false
            ? $user['contact'][$contact_conditions['name']]
            : null;

        $contact_conditions['name'] = 'facebook';
        $user['contact'][$contact_conditions['name']] = $table_methods->get_one_contact($contact_conditions);
        $user['contact'][$contact_conditions['name']] = $user['contact'][$contact_conditions['name']] !== false
            ? $user['contact'][$contact_conditions['name']]
            : null;

        $bank_conditions = [
            'relation_id' => $params['user_id'],
            'relation_type' => $this->container->get('relations')['users'],
            'state' => 1
        ];
        $get_bank = $table_methods->get_one_bank($bank_conditions);

        if (isset($get_bank['id'])) {
            $adress_conditions = [
                'relation_id' => $get_bank['id'],
                'relation_type' => $this->container->get('relations')['bank'],
                'state' => 1
            ];
            $get_adress = $table_methods->get_one_adress($adress_conditions);

            $get_bank['street'] = null;
            $get_bank['zip'] = null;
            $get_bank['city'] = null;
            $get_bank['country'] = null;

            if (isset($get_adress['id'])) {
                $get_bank['street'] = $get_adress['street'];
                $get_bank['zip'] = $get_adress['zip'];
                $get_bank['city'] = $get_adress['city'];
                $get_bank['country'] = $get_adress['country'];
            }
        }

        $user['bank'] = $get_bank;
        $user['bank'] = $user['bank'] !== false
            ? $user['bank']
            : null;

        return $response->withJson($user);
    }


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
    public function upload_user_image($request, $response){
        $params = $request->getParams();
        $upload_class = new Uploads($this->container);

        $required_params = ['file_type', 'user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $updated_data = Helper::is_updated($upload_class->upload_user_image($request));
        return $response->withJson( $updated_data );
    }

    /* updates and returns true or false
     */
    public function delete_user_image($request, $response){
        $params = $request->getParams();
        $upload_class = new Uploads($this->container);

        $required_params = ['user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $updated_data = Helper::is_updated( $upload_class->delete_user_image($params['user_id']));
        return $response->withJson( $updated_data );
    }



    /* creates and returns true or false
     */
    public function create_user_secret($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['hash', 'user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $params['relation_id'] = $params['user_id'];
        $params['relation_type'] = $this->container->get('relations')['users'];
        $params['type'] = $this->container->get('secret')['password'];

        $old_password = $table_methods->get_one_secret(
            [
                'relation_type' => $params['relation_type'],
                'relation_id' => $params['relation_id'],
                'type' => $params['type'],
                'state' => 1
            ]
        );

        if(empty($old_password)){
            $created_data = Helper::is_created($table_methods->create_secret($params));
            return $response->withJson( $created_data );
        }

        else{
            if(password_verify($params['old_hash'], $old_password['hash'])){
                $new_password = $table_methods->create_secret($params);
                if(isset($new_password['create'])){
                    $updated_data = Helper::is_updated($table_methods->update_state_secret(0, $params['uid'], ['id' => $old_password['id']]));
                    return $response->withJson( $updated_data );
                }
                else{
                    return $response->withJson(
                        array('errors' => $this->container->get('state')['INVALID_PASSWORD'])
                    );
                }
            }
            else {
                return $response->withJson(
                    array('errors' => $this->container->get('state')['INVALID_PASSWORD'])
                );
            }
        }
    }



    /* returns a multi array of requested data
     */
    public function get_user_adress($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['type', 'user_id'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        return $response->withJson( $table_methods->get_one_adress(
            [
                'relation_id' => $params['user_id'],
                'relation_type' => $this->container->get('relations')['users'],
                'type' => $params['type'],
                'state' => 1
            ]
        ));
    }

    /* creates and returns true or false
     */
    public function create_user_adress($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['type', 'street', 'zip','city','country','user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $params['relation_id'] = $params['user_id'];
        $params['relation_type'] = $this->container->get('relations')['users'];

        $created_data = Helper::is_created($table_methods->create_adress($params));
        return $response->withJson( $created_data );
    }

    /* updates and returns true or false
    */
    public function update_user_adress($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['id', 'user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $params['relation_id'] = $params['user_id'];
        $params['relation_type'] = $this->container->get('relations')['users'];

        $conditions['id'] = $params['id'];

        $updated_data = Helper::is_updated($table_methods->update_adress($params));
        return $response->withJson( $updated_data );
    }



    /* returns a multi array of requested data
     */
    public function get_user_contact($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['name','user_id'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        return $response->withJson( $table_methods->get_one_contact(
            [
                'relation_id' => $params['user_id'],
                'relation_type' => $this->container->get('relations')['users'],
                'name' => $params['name'],
                'state' => 1
            ]
        ));
    }

    /* creates and returns true or false
        */
    public function create_user_contact($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['name', 'value', 'user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $params['relation_id'] = $params['user_id'];
        $params['relation_type'] = $this->container->get('relations')['users'];

        $created_data = Helper::is_created($table_methods->create_contact($params));
        return $response->withJson( $created_data );
    }

    /* updates and returns true or false
    */
    public function update_user_contact($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $conditions['id'] = $params['id'];

        $updated_data = Helper::is_updated($table_methods->update_contact($params, $conditions));
        return $response->withJson( $updated_data );
    }



    /* returns a multi array of requested data
     */
    public function get_user_bank($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['user_id'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $bank_conditions = [
            'relation_id' => $params['user_id'],
            'relation_type' => $this->container->get('relations')['users'],
            'state' => 1
        ];
        $get_bank = $table_methods->get_one_bank( $bank_conditions );

        if(isset($get_bank['id'])){
            $adress_conditions = [
                'relation_id' => $get_bank['id'],
                'relation_type' => $this->container->get('relations')['bank'],
                'state' => 1
            ];
            $get_adress = $table_methods->get_one_adress( $adress_conditions );

            if(isset($get_adress['id'])){
                $get_bank['street'] = $get_adress['street'];
                $get_bank['zip'] = $get_adress['zip'];
                $get_bank['city'] = $get_adress['city'];
                $get_bank['country'] = $get_adress['country'];
            }
        }

        return $response->withJson( $get_bank );
    }

    /* creates and returns true or false
        */
    public function create_user_bank($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = [
            'name', 'iban', 'swift', 'user_id', 'uid',
            'street', 'zip', 'city', 'country'
        ];

        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $params['relation_id'] = $params['user_id'];
        $params['relation_type'] = $this->container->get('relations')['users'];
        $created_bank = $table_methods->create_bank($params);
        if(isset($created_bank['errors'])){
            return $response->withJson( $created_bank );
        }

        $adress_params = [
            'relation_id' => $created_bank['create']['id'],
            'relation_type' => $this->container->get('relations')['bank'],
            'street' => $params['street'],
            'zip' => $params['zip'],
            'city' => $params['city'],
            'country' => $params['country'],
            'uid' => $params['uid']
        ];
        $created_data = Helper::is_created($table_methods->create_adress($adress_params));
        return $response->withJson( $created_data );
    }

    /* updates and returns true or false
    */
    public function update_user_bank($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['id', 'user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $bank_params = [
            'uid' => $params['uid']
        ];
        if(isset($params['iban'])){ $bank_params['iban'] = $params['iban']; }
        if(isset($params['swift'])){ $bank_params['swift'] = $params['swift']; }
        if(isset($params['name'])){ $bank_params['name'] = $params['name']; }
        $conditions['id'] = $params['id'];

        $updated_bank = $table_methods->update_bank($bank_params, $conditions);
        if(isset($updated_bank['errors'])){
            return $response->withJson( $updated_bank );
        }

        $adress_params = [
            'uid' => $params['uid']
        ];
        if(isset($params['street'])){ $adress_params['street'] = $params['street']; }
        if(isset($params['zip'])){ $adress_params['zip'] = $params['zip']; }
        if(isset($params['city'])){ $adress_params['city'] = $params['city']; }
        if(isset($params['country'])){ $adress_params['country'] = $params['country']; }
        $adress_conditions = [
            'relation_id' => $params['id'],
            'relation_type' => $this->container->get('relations')['bank'],
        ];

        $update_data = Helper::is_updated($table_methods->update_adress($adress_params, $adress_conditions));

        return $response->withJson( $update_data );
    }



    /* returns a multi array of requested data
     */
    public function get_user_ip($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['user_id'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        return $response->withJson( $table_methods->get_one_ip(
            [
                'relation_id' => $params['user_id'],
                'relation_type' => $this->container->get('relations')['users'],
                'state' => 1
            ]
        ));
    }

    /* creates and returns true or false
        */
    public function create_user_ip($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['ip', 'user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $params['relation_id'] = $params['user_id'];
        $params['relation_type'] = $this->container->get('relations')['users'];

        $created_data = Helper::is_created($table_methods->create_ip($params));
        return $response->withJson( $created_data );
    }
}
