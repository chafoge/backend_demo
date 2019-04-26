<?php
namespace src\Requests;
use src\Database\Tables\Table_Methods;
use src\Database\Taxonomies\Taxonomie_Methods;
use src\Library\Helper;
use src\Requests\Request_Controller;

class User_vita extends Request_Controller
{
    /* returns a multi array of requested data
     */
    public function get_user_vitas($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['user_id'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $order = 'ORDER BY create_date DESC';

        $vita_conditions = [
            'state' => 1,
            'relation_id' => $params['user_id'],
            'relation_type' => $this->container->get('relations')['users']
        ];
        $columns = ['id', 'name', 'value'];
        $vitas = $taxonomie_methods->get_all_vitas($vita_conditions, $columns, $order);

        $r_vita = [];
        foreach ($vitas as $v_key => $vita){
            $date_conditions = [
                'relation_id' => $vita['id'],
                'relation_type' => $this->container->get('relations')['vita']
            ];
            $columns = ['name', 'value'];
            $vitas[$v_key]['date'] = $taxonomie_methods->get_one_date($date_conditions, $columns, $order);

            $company_relation_conditions = [
                    'state' => 1,
                    'object_type' => $this->container->get('relations')['users'],
                    'relation_type' => $this->container->get('relations')['vita'],
                    'relation_id' => $vita['id'],
            ];
            $company_conditions = [
                'id' => $table_methods->get_one_relation($company_relation_conditions,['object_id'])['object_id']
            ];
            $columns = ['id', 'name'];
            $vitas[$v_key]['company'] = $table_methods->get_one_user($company_conditions, $columns, $order);

            $adress_conditions = [
                'relation_id' => $vita['id'],
                'relation_type' => $this->container->get('relations')['vita']
            ];
            $columns = ['street', 'zip', 'city', 'country'];
            $vitas[$v_key]['adress'] = $table_methods->get_all_adress($adress_conditions, $columns, $order);


            $r_vita[$v_key] = [
                'id' => $vita['id'],
                'type' => $this->container->get('relations')['vita'],
                'description' => $vita['value'],
                'position' => $vita['name'],
                'start_date' => $vitas[$v_key]['date']['name'],
                'end_date' => $vitas[$v_key]['date']['value'],
                'company' => $vitas[$v_key]['company']['name'],
                'adress' => $vitas[$v_key]['adress']
//                'street' => $vitas[$v_key]['adress']['street'],
//                'zip' => $vitas[$v_key]['adress']['zip'],
//                'city' => $vitas[$v_key]['adress']['city'],
//                'country' => $vitas[$v_key]['adress']['country']
            ];


        }

        return $response->withJson( $r_vita );
    }


    /* creates and returns true or false
     */
    public function create_user_vita($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = [
            'position', 'description', 'user_id', 'uid',
            'company',
            'street', 'zip', 'city', 'country',
            'start_date', 'end_date'
        ];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        //create vita
        $vita_params = [
            'relation_type' => $this->container->get('relations')['users'],
            'relation_id' => $params['user_id'],
            'name' => $params['position'],
            'value' => $params['description'] ,
            'uid' => $params['uid']
        ];
        $created_vita = $taxonomie_methods->create_vita($vita_params);
        if(isset($created_vita['errors'])){
            return $response->withJson( $created_vita );
        }

        //if vita is created create addional information
        if(isset($created_vita['create']['id'])) {
            //create date
            $date_params = [
                'relation_type' => $this->container->get('relations')['vita'],
                'relation_id' => $created_vita['create']['id'],
                'name' => $params['start_date'],
                'value' => $params['end_date'],
                'uid' => $params['uid']
            ];
            $created_date = $taxonomie_methods->create_date($date_params);
            if(isset($created_date['errors'])){
                return $response->withJson( $created_date );
            }

            //check if company exists
            $company_params = [
                'type' => $this->container->get('users_type')['company'],
                'name' => $params['company'],
            ];
            $get_company = $table_methods->get_one_user($company_params);

            //if company exists create relation
            if(isset($get_company['id'])){
                $company_relation_params = [
                    'object_type' => $this->container->get('relations')['users'],
                    'object_id' => $get_company['id'],
                    'relation_type' => $this->container->get('relations')['vita'],
                    'relation_id' => $created_vita['create']['id'],
                    'uid' => $params['uid']
                ];
                $created_company_relation = $table_methods->create_relation($company_relation_params);
                if(isset($created_company_relation['errors'])){
                    return $response->withJson( $created_company_relation );
                }
            }
            //if not create company and then relaton
            else{
                $company_params['relation_type'] = $this->container->get('relations')['vita'];
                //$company_params['relation_id'] = $created_vita['create']['id'];
                $company_params['uid'] = $params['uid'];
                $created_company = $table_methods->create_user($company_params);

                if(isset($created_company['create']['id'])) {
                    $company_relation_params = [
                        'object_type' => $this->container->get('relations')['users'],
                        'object_id' => $created_company['create']['id'],
                        'relation_type' => $this->container->get('relations')['vita'],
                        'relation_id' => $created_vita['create']['id'],
                        'uid' => $params['uid']
                    ];
                    $created_company_relation = $table_methods->create_relation($company_relation_params);
                    if(isset($created_company_relation['errors'])){
                        return $response->withJson( $created_company_relation );
                    }
                }
            }

            //add vita adress
            $adress_params = [
                'relation_type' => $this->container->get('relations')['vita'],
                'relation_id' => $created_vita['create']['id'],
                'street' => $params['street'],
                'zip' => $params['zip'],
                'city' => $params['city'],
                'country' => $params['country'],
                'uid' => $params['uid']
            ];
            $created_adress = $table_methods->create_adress($adress_params);
            if(isset($created_adress['errors'])){
                return $response->withJson( $created_adress );
            }
        }

        return $response->withJson( array('create' => true) );
    }

    /* updates and returns true or false
     */
    public function update_user_vita($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $vita_conditions = [
            'relation_type' => $this->container->get('relations')['users'],
            'relation_id' => $params['user_id'],
        ];

        $vita_params = [
            'uid' => $params['uid']
        ];

        if(isset($params['position'])){ $vita_params['name'] = $params['position']; }
        if(isset($params['description'])){ $vita_params['value'] = $params['description']; }

        $updated_vita = $taxonomie_methods->update_vita($vita_params, $vita_conditions);

        $updated_data = Helper::is_updated( $updated_vita );
        return $response->withJson( $updated_data );
    }


    /* creates and returns true or false
     */
    public function delete_user_vita($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['vita_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $conditions['id'] = $params['vita_id'];

        $updated_data = Helper::is_updated( $taxonomie_methods->update_state_vita(0, $params['uid'], $conditions) );
        return $response->withJson( $updated_data );
    }

    /* creates and returns true or false
     */
    public function create_user_vita_date($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['vita_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $date_params = [
            'relation_type' => $this->container->get('relations')['vita'],
            'relation_id' => $params['vita_id'],
            'name' => $params['start_date'],
            'value' => $params['end_date'],
            'uid' => $params['uid']
        ];

        $created_data = Helper::is_created( $taxonomie_methods->create_date($date_params) );
        return $response->withJson( $created_data );
    }

    /* updates and returns true or false
     */
    public function update_user_vita_date($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['vita_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $date_conditions = [
            'relation_type' => $this->container->get('relations')['vita'],
            'relation_id' => $params['vita_id'],
        ];

        $date_params = [
            'uid' => $params['uid']
        ];

        if(isset($params['start_date'])){ $date_params['name'] = $params['start_date']; }
        if(isset($params['end_date'])){ $date_params['value'] = $params['end_date']; }

        $updated_vita = $taxonomie_methods->update_vita($date_params, $date_conditions);

        $updated_data = Helper::is_updated( $updated_vita );
        return $response->withJson( $updated_data );
    }

    /* creates and returns true or false
     */
    public function create_user_vita_company($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['company', 'vita_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $get_company_conditions = [
            'type' => $this->container->get('users_type')['company'],
            'name' => $params['company'],
        ];
        $get_company = $table_methods->get_one_company($get_company_conditions);

        if(isset($get_company['id'])){
            $company_relation_params = [
                'object_type' => $this->container->get('relations')['users'],
                'object_id' => $get_company['id'],
                'relation_type' => $this->container->get('relations')['vita'],
                'relation_id' => $params['vita_id'],
                'uid' => $params['uid']
            ];
            $created_company_relation = $table_methods->create_relation($company_relation_params);
            if(isset($created_company_relation['errors'])){
                return $response->withJson( $created_company_relation );
            }
        }
        else {
            $company_params = [
                'type' => $this->container->get('users_type')['company'],
                'relation_type' => $this->container->get('relations')['vita'],
                'relation_id' => $params['vita_id'],
                'name' => $params['company'],
                'uid' => $params['uid']
            ];
            $created_company = Helper::is_created( $table_methods->create_user($company_params) );
            if(isset($created_company_relation['errors'])){
                return $response->withJson( $created_company );
            }

            $company_relation_params = [
                'object_type' => $this->container->get('relations')['users'],
                'object_id' => $created_company['create']['id'],
                'relation_type' => $this->container->get('relations')['vita'],
                'relation_id' => $params['vita_id'],
                'uid' => $params['uid']
            ];
            $created_company_relation = $table_methods->create_relation($company_relation_params);
            return $response->withJson( $created_company_relation );
        }
    }

    /* updates and returns true or false
    */
    public function update_user_vita_company($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['company', 'vita_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        //check company exists
        $company_conditions = [
            'type' => $this->container->get('users_type')['company'],
            'name' => $params['company']
        ];
        $get_company = $table_methods->get_one_user($company_conditions);

        if(isset($get_company['id'])) {
            $company = $get_company;
        }
        else {
           //create company
            $company_params = [
                'type' => $this->container->get('users_type')['company'],
                'name' => $params['company'],
                'relation_type' => $this->container->get('relations')['vita'],
                'relation_id' => $params['vita_id'],
                'uid' => $params['uid']
            ];
            $company = $table_methods->create_user($company_params);
            if(isset($company['errors'])){
                return $response->withJson( $company );
            }
        }

       $company = isset($company['create'])
           ? $company['create']
           : $company;

        //check relation exists
        $relation_conditions = [
            'object_type' => $this->container->get('relations')['users'],
            'relation_type' => $this->container->get('relations')['vita'],
            'relation_id' => $params['vita_id'],
        ];

        //set old relation state 0
        $update_state_relation = $table_methods->update_state_relation(0, $params['uid'], $relation_conditions);
        if (isset($update_state_relation['errors'])) {
            return $response->withJson($update_state_relation);
        }

        //create new relation
        $relation_conditions['object_id'] = $company['id'];
        $relation_conditions['uid'] = $params['uid'];
        $create_relation = $table_methods->create_relation($relation_conditions);

        $updated_data = Helper::is_updated( $create_relation );
        return $response->withJson( $updated_data );
    }

    /* creates and returns true or false
     */
    public function create_user_vita_adress($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = [ 'street', 'zip', 'city', 'country', 'vita_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $params['relation_type'] = $this->container->get('relations')['vita'];
        $params['relation_id'] = $params['vita_id'];

        $created_data = Helper::is_created( $table_methods->create_adress($params) );
        return $response->withJson( $created_data );
    }

    /* updates and returns true or false
    */
    public function update_user_vita_adress($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = [ 'vita_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $conditions['relation_type'] =$this->container->get('relations')['vita'];
        $conditions['relation_id'] = $params['vita_id'];

        $updated_data = Helper::is_updated( $table_methods->update_adress($params, $conditions) );
        return $response->withJson( $updated_data );
    }
}
